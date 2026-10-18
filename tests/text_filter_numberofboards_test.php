<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace filter_jsxgraph;

/**
 * Tests for the validation of the numberOfBoards attribute.
 *
 * @package    filter_jsxgraph
 * @category   test
 * @copyright  2026 ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_jsxgraph\text_filter
 */
final class text_filter_numberofboards_test extends \advanced_testcase {
    /**
     * Counts the boards in the output of the filter for a tag with the given numberOfBoards value.
     *
     * @param string $value Value of the attribute numberOfBoards.
     * @return int
     */
    private function count_boards(string $value): int {
        $filter = new text_filter(\context_system::instance(), []);
        $output = $filter->filter('<jsxgraph numberOfBoards="' . $value . '">/* code */</jsxgraph>');

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($output);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);
        return $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " jxgbox ")]')->length;
    }

    /**
     * A huge or invalid number of boards must not drive the construction loop.
     */
    public function test_number_of_boards_is_limited_violation(): void {
        $this->resetAfterTest();

        // The limit is 24 boards; 1000 would be built completely without the limit.
        $this->assertSame(24, $this->count_boards('1000'));
        $this->assertSame(1, $this->count_boards('-5'));
        $this->assertSame(1, $this->count_boards('abc'));
    }

    /**
     * A reasonable number of boards is still built completely.
     */
    public function test_number_of_boards_is_limited_verification(): void {
        $this->resetAfterTest();

        $this->assertSame(1, $this->count_boards('1'));
        $this->assertSame(3, $this->count_boards('3'));
    }
}
