<?php
require_once('Autoload.php');
class FilterTest extends PHPUnit_Framework_TestCase
{
    public function testParsing()
    {
        $filter = new \Data\Filter('a eq b');
        $clauses = $filter->getChildren();
        $this->assertCount(1, $clauses);
        $clause = $clauses[0];
        $this->assertEquals($clause->op, '=');
        $this->assertEquals($clause->var1, 'a');
        $this->assertEquals($clause->var2, 'b');

        $filter = new \Data\Filter("a eq 'b'");
        $clauses = $filter->getChildren();
        $this->assertCount(1, $clauses);
        $clause = $clauses[0];
        $this->assertEquals($clause->op, '=');
        $this->assertEquals($clause->var1, 'a');
        $this->assertEquals($clause->var2, "'b'");

        $filter = new \Data\Filter('a eq 1');
        $clauses = $filter->getChildren();
        $this->assertCount(1, $clauses);
        $clause = $clauses[0];
        $this->assertEquals($clause->op, '=');
        $this->assertEquals($clause->var1, 'a');
        $this->assertEquals($clause->var2, '1');

        $filter = new \Data\Filter('a ne b');
        $clauses = $filter->getChildren();
        $this->assertCount(1, $clauses);
        $clause = $clauses[0];
        $this->assertEquals($clause->op, '!=');
        $this->assertEquals($clause->var1, 'a');
        $this->assertEquals($clause->var2, 'b');

        $filter = new \Data\Filter('a gt b');
        $clauses = $filter->getChildren();
        $this->assertCount(1, $clauses);
        $clause = $clauses[0];
        $this->assertEquals($clause->op, '>');
        $this->assertEquals($clause->var1, 'a');
        $this->assertEquals($clause->var2, 'b');

        $filter = new \Data\Filter('a ge b');
        $clauses = $filter->getChildren();
        $this->assertCount(1, $clauses);
        $clause = $clauses[0];
        $this->assertEquals($clause->op, '>=');
        $this->assertEquals($clause->var1, 'a');
        $this->assertEquals($clause->var2, 'b');

        $filter = new \Data\Filter('a lt b');
        $clauses = $filter->getChildren();
        $this->assertCount(1, $clauses);
        $clause = $clauses[0];
        $this->assertEquals($clause->op, '<');
        $this->assertEquals($clause->var1, 'a');
        $this->assertEquals($clause->var2, 'b');

        $filter = new \Data\Filter('a le b');
        $clauses = $filter->getChildren();
        $this->assertCount(1, $clauses);
        $clause = $clauses[0];
        $this->assertEquals($clause->op, '<=');
        $this->assertEquals($clause->var1, 'a');
        $this->assertEquals($clause->var2, 'b');
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
