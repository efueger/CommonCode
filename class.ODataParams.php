<?php
class ODataParams
{
    public $filter = false;
    public $expand = false;
    public $select = false;
    public $orderby = false;
    public $top = false;
    public $skip = false;
    public $count = false;
    public $search = false;

    function __construct($params)
    {
        if(isset($params['filter']))
        {
            $this->filter = new \Data\Filter($params['filter']);
        }
        else if(isset($params['$filter']))
        {
            $this->filter = new \Data\Filter($params['$filter']);
        }

        if(isset($params['$expand']))
        {
            $this->expand = explode(',',$params['$expand']);
        }

        if(isset($params['select']))
        {
            $this->select = explode(',',$params['select']);
        }
        else if(isset($params['$select']))
        {
            $this->select = explode(',',$params['$select']);
        }

        if(isset($params['$orderby']))
        {
            $this->orderby = array();
            $orderby = explode(',',$params['$orderby']);
            $count = count($orderby);
            for($i = 0; $i < $count; $i++)
            {
                $exp = explode(' ',$orderby[$i]);
                if(count($exp) === 1)
                {
                    //Default to assending
                    $this->orderby[$exp[0]] = 1;
                }
                else
                {
                    switch($exp[1])
                    {
                        case 'asc':
                            $this->orderby[$exp[0]] = 1;
                            break;
                        case 'desc':
                            $this->orderby[$exp[0]] = -1;
                            break;
                        default:
                            throw new Exception('Unknown orderby operation');
                    }
                }
            }
        }

        if(isset($params['$top']))
        {
            $this->top = $params['$top'];
        }

        if(isset($params['$skip']))
        {
            $this->skip = $params['$skip'];
        }

        if(isset($params['$count']) && $params['$count'] === 'true')
        {
            $this->count = true;
        }

        if(isset($params['$seach']))
        {
            throw new Exception('Search not yet implemented');
        }
    }
}
?>
