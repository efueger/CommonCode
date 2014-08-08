<?php
class FlipsideCAPTCHA 
{
    private $random_id;
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

    public function __construct()
    {
        $this->random_id = mt_rand(0, count($this->questions)-1);
    }

    public function get_question()
    {
        return $this->questions[$this->random_id];
    }

    public function is_answer_right($answer)
    {
        if(strcasecmp($answer, $this->answers[$this->random_id]) == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function draw_captcha($explination = true, $return = false)
    {
        $string = '';
        if($explination)
        {
            $string .= 'The answer to this question may be found in the Burning Flipside Survival Guide. It may be found <a href="http://www.burningflipside.com/sg">here</a>.<br/>';
        }

        $string .= '<form id="flipcaptcha" name="flipcaptcha">
            <label for="captcha">'.$this->get_question().'</label><input type="text" id="captcha" name="captcha"/>
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
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
