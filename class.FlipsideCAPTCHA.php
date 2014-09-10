<?php
require_once('class.JsonSerializable.php');
require_once("class.FlipsideDB.php");
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
        $res = array();
        $data = FlipsideDB::seleect_all_from_db('registration', 'captcha', 'id');
        if($data == FALSE)
        {
            return FALSE;
        }
        for($i = 0; $i < count($data); $i++)
        {
            $res[$i] = $data[$i]['id'];
        }
        return $res;
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
        FlipsideDB::write_to_db('registration', 'captcha', array('question'=>$question,'hint'=>$hint,'answer'=>$answer));
        $data = FlipsideDB::select_field('registration', 'captcha', 'id', array('question'=>'="'.$question.'"'));
        if($data == FALSE)
        {
            return FALSE; 
        }
        return $data['id'];
    }

    public function __construct()
    {
        $this->valid_ids = FlipsideCAPTCHA::get_valid_captcha_ids();
        $this->random_id = mt_rand(0, count($this->valid_ids)-1);
        $this->random_id = $this->valid_ids[$this->random_id];
    }

    public function get_question()
    {
        $data = FlipsideDB::select_field('registration', 'captcha', 'question', array('id'=>'="'.$this->random_id.'"'));
        if($data == FALSE)
        {
            return FALSE;
        }
        return $data['question'];
    }

    public function get_hint()
    {
        $data = FlipsideDB::select_field('registration', 'captcha', 'hint', array('id'=>'="'.$this->random_id.'"'));
        if($data == FALSE)
        {
            return FALSE;
        }
        return $data['hint'];
    }

    private function get_answer()
    {
        $data = FlipsideDB::select_field('registration', 'captcha', 'answer', array('id'=>'="'.$this->random_id.'"'));
        if($data == FALSE)
        {
            return FALSE;
        }
        return $data['answer'];
    }

    public function is_answer_right($answer)
    {
        return $this->get_answer() == $answer;
    }

    public function draw_captcha($explination = true, $return = false)
    {
        $string = '';
        if($explination)
        {
            $string .= 'The answer to this question may be found in the Burning Flipside Survival Guide. It may be found <a href="http://www.burningflipside.com/sg">here</a>.<br/>';
        }

        $string .= '<form id="flipcaptcha" name="flipcaptcha">
            <label for="captcha">'.$this->get_question().'</label><input type="text" id="captcha" name="captcha"/> '.$this->get_hint().'
        </form>';
        
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
