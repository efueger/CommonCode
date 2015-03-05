<?php
require_once('class.FlipSession.php');
require_once('class.FlipsideUser.php');
require_once('PHPExcel/PHPExcel.php');
require_once('XML/Serializer.php');
require_once('Slim/Slim.php');
\Slim\Slim::registerAutoloader();

const SUCCESS = 0;
const UNRECOGNIZED_METHOD = 1;
const INVALID_PARAM = 2;
const ALREADY_LOGGED_IN = 3;
const INVALID_LOGIN = 4;
const ACCESS_DENIED = 5;
const INTERNAL_ERROR = 6;

const UNKNOWN_ERROR = 255;

class OAuth2Auth extends \Slim\Middleware
{
    protected $headers = array();

    public function __construct($headers)
    {
        $this->headers = $headers;
    }

    public function call()
    {
        // no auth header
        if(!isset($this->headers['Authorization']))
        {
            $this->app->getLog()->error("No authorization header");
            if(FlipSession::is_logged_in())
            {
                $user = FlipSession::get_user(TRUE);
                $this->app->user = $user;
            }
        } 
        else 
        {
            try
            {
                $key = substr($this->headers['Authorization'], 7);
                $user = FlipsideUser::getUserByAccessCode($key);
                if($user !== FALSE)
                {
                    $this->app->user = $user;
                }
            }
            catch(\Exception $e)
            {
            }
        }

        // this line is required for the application to proceed
        $this->next->call();
    }
}

class FlipRESTFormat extends \Slim\Middleware
{
    private function fix_encoded_element($key, $value, &$array, $prefix = '')
    {
        if(is_array($value))
        {
            $array[$key] = implode(';', $value);
        }
        else if($key === '_id' && is_object($value))
        {
            $array[$key] = $value->{'$id'};
        }
        else if(is_object($value))
        {
            $array[$key] = $this->app->request->getUrl().$this->app->request->getPath().$prefix.'/'.$key;
        }
        else if(strncmp($value, 'data:', 5) === 0)
        {
            $array[$key] = $this->app->request->getUrl().$this->app->request->getPath().$prefix.'/'.$key;
        }
    }

    private function create_csv(&$array)
    {
        if (count($array) == 0)
        {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        if(is_array($array))
        {
            $first = reset($array);
            $keys = FALSE;
            if(is_array($first))
            {
                $keys = array_keys($first);
            }
            else if(is_object($first))
            {
                $keys = array_keys(get_object_vars($first));
            }
            fputcsv($df, $keys);
            foreach ($array as $row)
            {
                if(is_array($row))
                {
                    $id = $row[$keys[0]];
                    foreach($row as $key=>$value)
                    {
                        $this->fix_encoded_element($key, $value, &$row, '/'.$id);
                    }
                    fputcsv($df, $row);
                }
                else if(is_object($row))
                {
                    $id = $row->$keys[0];
                    if(is_object($id))
                    {
                        $id = $id->{'$id'};
                    }
                    $values = get_object_vars($row);
                    foreach($values as $key=>$value)
                    {
                        $this->fix_encoded_element($key, $value, &$values, '/'.$id);
                    }
                    fputcsv($df, $values);
                }
            }
        }
        else
        {
            $array = get_object_vars($array);
            fputcsv($df, array_keys($array));
            foreach($array as $key=>$value)
            {
                $this->fix_encoded_element($key, $value, &$array);
            }
            fputcsv($df, $array);
        }
        fclose($df);
        return ob_get_clean();
    }

    private function create_xlsx(&$array)
    {
        $xlsx = new PHPExcel();
    }

    private function create_xml(&$array, $path)
    {
        if(is_array($array) && is_object($array[0]))
        {
            $count = count($array);
            for($i = 0; $i < $count; $i++)
            {
                if(property_exists($array[$i], '_id') && is_object($array[$i]->_id))
                {
                    $array[$i]->_id = $array[$i]->_id->{'$id'};
                }
            }
        }
        else if(is_object($array))
        {
            if(property_exists($array, '_id') && is_object($array->_id))
            {
                $array->_id = $array->_id->{'$id'};
            }
        }
        $options = array(
            XML_SERIALIZER_OPTION_ROOT_NAME   => $path,
            XML_SERIALIZER_OPTION_DEFAULT_TAG => substr($path, 0, strlen($path)-1)
        );
        $serializer = new XML_Serializer($options);
        $serializer->serialize($array);
        return $serializer->getSerializedData();
    }

    public function call()
    {
        if($this->app->request->isOptions())
        {
            return;
        }
        $fmt = $this->app->request->params('fmt');
        if($fmt === null)
        {
            $mime_type = $this->app->request->headers->get('Accept');
            switch($mime_type)
            {
                case 'text/csv':
                    $fmt = 'csv';
                    break;
                default:
                    $fmt = 'json';
                    break;
            }
        }

        $this->app->fmt = $fmt;

        $this->next->call();

        if($this->app->response->getStatus() == 200 && $this->app->fmt !== 'json')
        {
            $data = json_decode($this->app->response->getBody());
            $text = '';
            switch($this->app->fmt)
            {
                case 'data-table':
                    $this->app->response->headers->set('Content-Type', 'application/json');
                    $text = json_encode(array('data'=>$data));
                    break;
                case 'csv':
                    $this->app->response->headers->set('Content-Type', 'text/csv');
                    $path = $this->app->request->getPathInfo();
                    $path = strrchr($path, '/');
                    $path = substr($path, 1);
                    $this->app->response->headers->set('Content-Disposition', 'attachment; filename='.$path.'.csv');
                    $text = $this->create_csv($data);
                    break;
                case 'xlsx':
                    $this->app->response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    $path = $this->app->request->getPathInfo();
                    $path = strrchr($path, '/');
                    $path = substr($path, 1);
                    $this->app->response->headers->set('Content-Disposition', 'attachment; filename='.$path.'.xslx');
                    $text = $this->create_xslx($data);
                    break;
                case 'xml':
                    $this->app->response->headers->set('Content-Type', 'application/xml');
                    $path = $this->app->request->getPathInfo();
                    $path = strrchr($path, '/');
                    $path = substr($path, 1);
                    $text = $this->create_xml($data, $path);
                    break;
                case 'passthru':
                    $text = $this->app->response->getBody();
                    break;
                default:
                    $text = 'Unknown fmt '.$fmt;
                    break;
            }
            $this->app->response->setBody($text);
        }
        else if($this->app->response->getStatus() == 200)
        {
            $this->app->response->headers->set('Content-Type', 'application/json');
        }
    }
}

class FlipREST extends \Slim\Slim
{
    function __construct()
    {
        parent::__construct();
        $this->config('debug', false);
        $headers = apache_request_headers();
        $this->add(new OAuth2Auth($headers));
        $this->add(new FlipRESTFormat());
        $error_handler = array($this, 'error_handler');
        $this->error($error_handler);
    }

    function route_get($uri, $handler)
    {
        return $this->get($uri, $handler);
    }

    function route_post($uri, $handler)
    {
        return $this->post($uri, $handler);
    }

    function error_handler($e)
    {
        $error = array(
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        );
        $this->response->headers->set('Content-Type', 'application/json');
        echo json_encode($error);
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
