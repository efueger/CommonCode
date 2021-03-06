<?php
require_once('Autoload.php');
class FlipsideCAPTCHA implements JsonSerializable
{
    public  $random_id;
    private $valid_ids;
    /*TODO - Replace with DB or something */
    private $questions = array('How much water should you bring to Burning Flipside?',
                               'Will you bring anything to sell at the event?',
                               'What does "no" mean?',
                               'What does MOOP stand for?'
                              );
    private $answers   = array('3 gallons per day',
                               'no',
                               'no',
                               'matter out of place');

    public static function get_valid_captcha_ids()
    {
        $dataset = DataSetFactory::get_data_set('profiles');
        $datatable = $dataset['captcha'];
        $data = $datatable->read(false, array('id'));
        $count = count($data);
        for($i = 0; $i < $count; $i++)
        {
            $data[$i] = $data[$i]['id'];
        }
        return $data;
    }

    public static function get_all()
    {
        $res = array();
        $ids = FlipsideCAPTCHA::get_valid_captcha_ids();
        for($i = 0; $i < count($ids); $i++)
        {
            $captcha = new FlipsideCAPTCHA();
            $captcha->random_id = $ids[$i]; 
            array_push($res, $captcha);
        }
        return $res;
    }

    public static function save_new_captcha($question, $hint, $answer)
    {
        $dataset = DataSetFactory::get_data_set('profiles');
        $datatable = $dataset['captcha'];
        return $datatable->create(array('question'=>$question,'hint'=>$hint,'answer'=>$answer));
    }

    public function __construct()
    {
        $this->valid_ids = FlipsideCAPTCHA::get_valid_captcha_ids();
        $this->random_id = mt_rand(0, count($this->valid_ids)-1);
        $this->random_id = $this->valid_ids[$this->random_id];
    }

    public function get_question()
    {
        $dataset = DataSetFactory::get_data_set('profiles');
        $datatable = $dataset['captcha'];
        $data = $datatable->read(new \Data\Filter('id eq '.$this->random_id), array('question'));
        if($data === false)
        {
            return false;
        }
        return $data[0]['question'];
    }

    public function get_hint()
    {
        $dataset = DataSetFactory::get_data_set('profiles');
        $datatable = $dataset['captcha'];
        $data = $datatable->read(new \Data\Filter('id eq '.$this->random_id), array('hint'));
        if($data === false)
        {
            return false;
        }
        return $data[0]['hint'];
    }

    private function get_answer()
    {
        $dataset = DataSetFactory::get_data_set('profiles');
        $datatable = $dataset['captcha'];
        $data = $datatable->read(new \Data\Filter('id eq '.$this->random_id), array('answer'));
        if($data === false)
        {
            return false;
        }
        return $data[0]['answer'];
    }

    public function is_answer_right($answer)
    {
        return strcasecmp($this->get_answer(),$answer) == 0;
    }

    public function draw_captcha($explination=true, $return=false, $own_form=false)
    {
        $string = '';

        if($own_form)
        {
            $string.= '<form id="flipcaptcha" name="flipcaptcha">';
        }

        $string .= '<label for="captcha" class="col-sm-2 control-label">'.$this->get_question().'</label><div class="col-sm-10"><input class="form-control" type="text" id="captcha" name="captcha" placeholder="'.$this->get_hint().'" required/></div>';
        if($own_form)
        {
            $string.='</form>';
        }
        if($explination)
        {
            $string .= '<div class="col-sm-10">The answer to this question may be found in the Burning Flipside Survival Guide. It may be found <a href="http://www.burningflipside.com/sg">here</a>.</div>';
        }
        
        if(!$return)
        {
            echo $string;
        }
        else
        {
            return $string;
        }
    }

    public function self_json_encode()
    {
        return json_encode($this->jsonSerialize());
    }

    public function jsonSerialize()
    {
        $res = array();
        $res['id'] = $this->random_id;
        $res['question'] = $this->get_question();
        $res['hint'] = $this->get_hint();
        $res['answer'] = $this->get_answer();
        return $res;
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
