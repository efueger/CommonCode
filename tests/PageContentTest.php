<?php
require_once('Autoload.php');
class PageContentTest extends PHPUnit_Framework_TestCase
{
    public function testPageTitle()
    {
        $GLOBALS['FLIPSIDE_SETTINGS_LOC'] = './tests/helpers';
        $GLOBALS['BROWSCAP_CACHE']        = './tests/helpers';
        $page = new FlipPage('Test');
        ob_start();
        $page->print_page();
        $html = ob_get_contents();
        ob_end_clean();
        
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        $elements = $doc->getElementsByTagName('title');
        $this->assertEquals(1, $elements->length); 
        $node = $elements->item(0);
        $this->assertEquals('Test', $node->nodeValue);

        $page = new FlipPage('Something Else');
        ob_start();
        $page->print_page();
        $html = ob_get_contents();
        ob_end_clean();

        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $elements = $doc->getElementsByTagName('title');
        $this->assertEquals(1, $elements->length);
        $node = $elements->item(0);
        $this->assertEquals('Something Else', $node->nodeValue);
    }

    public function testDefaultScripts()
    {
        $GLOBALS['FLIPSIDE_SETTINGS_LOC'] = './tests/helpers';
        $GLOBALS['BROWSCAP_CACHE']        = './tests/helpers';
        $page = new FlipPage('Test');
        ob_start();
        $page->print_page();
        $html = ob_get_contents();
        ob_end_clean();

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        $elements = $doc->getElementsByTagName('script');
        $this->assertEquals(4, $elements->length);
        $default = array('jquery', 'flipside', 'bootstrap');
        $defaultCount = count($default);
        for($i = 0; $i < $elements->length; $i++)
        {
            $node = $elements->item($i);
            if($node->hasAttribute('src'))
            {
                $attrib = $node->getAttribute('src');
                $ret = false;
                for($j = 0; $j < $defaultCount; $j++)
                {
                    $ret = strstr($attrib, $default[$j]);
                    if($ret !== false) break;
                }
                $this->assertNotFalse($ret);
            }
            else
            {
                $this->assertGreaterThan(0, strlen($node->nodeValue));
            }
        }
    }

    public function testIERendering()
    {
        $GLOBALS['FLIPSIDE_SETTINGS_LOC'] = './tests/helpers';
        $GLOBALS['BROWSCAP_CACHE']        = './tests/helpers';
        $page = new FlipPage('Test');
        $page->browser = new stdClass();
        $page->browser->Browser = 'IE';
        $page->browser->MajorVer = 9;
        ob_start();
        $page->print_page();
        $html = ob_get_contents();
        ob_end_clean();

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        $elements = $doc->getElementsByTagName('meta');
        $this->assertEquals(2, $elements->length);
        $node = $elements->item(0);
        $this->assertTrue($node->hasAttribute('http-equiv'));
        $value = $node->getAttribute('http-equiv');
        $this->assertEquals('X-UA-Compatible', $value);
        $this->assertTrue($node->hasAttribute('content'));
        $value = $node->getAttribute('content');
        $this->assertEquals('IE=edge', $value);

        $page = new FlipPage('Test');
        $page->browser = new stdClass();
        $page->browser->Browser = 'IE';
        $page->browser->MajorVer = 8;
        ob_start();
        $page->print_page();
        $html = ob_get_contents();
        ob_end_clean();

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        $elements = $doc->getElementsByTagName('script');
        $this->assertEquals(5, $elements->length);
        $found = false;
        for($i = 0; $i < $elements->length; $i++)
        {
            $node = $elements->item($i);
            if($node->hasAttribute('src'))
            {
                $attrib = $node->getAttribute('src');
                if(strstr($attrib, 'html5.js') !== false)
                {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found);
    }
}
?>
