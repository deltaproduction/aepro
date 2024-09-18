<?php

namespace App\Http\Controllers\Contest;

use App\Models\ContestMember;

class GetSeatsCounts
{
    public function getPlacesCountsInAuditorium($_counts, $columns, $rows) {
        $matrix = array_fill(0, $rows, array_fill(0, $columns, null));
        $all_places = [];

        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $columns; $j++) {
                $all_places[] = [$i, $j];
            }
        }

        $counts = [];

        foreach ($_counts as $school => $num_places) {
            $counts[] = [$school, $num_places];
        }

        usort($counts, function($a, $b) {
            return $b[1] - $a[1];
        });

        $busy_places = [];

        foreach ($counts as $data) {
            $school = $data[0];
            $num_places = $data[1];

            $forbidden_places = [];
            $busy_places_count = 0;

            for ($i = 0; $i < $num_places; $i++) {
                $f_places = array_merge($busy_places, $forbidden_places);

                $free_places = array_filter($all_places, function($place) use ($f_places) {
                    foreach ($f_places as $f_place) {
                        if ($place[0] == $f_place[0] && $place[1] == $f_place[1]) {
                            return false;
                        }
                    }
                    return true;
                });

                if (!empty($free_places)) {
                    $last_place = array_pop($free_places);
                    $r_row = $last_place[0];
                    $r_column = $last_place[1];
                    $busy_places_count++;

                    $matrix[$r_row][$r_column] = $school;
                    $busy_places[] = [$r_row, $r_column];
                }

                if ($r_column + 1 < $columns) {
                    $forbidden_places[] = [$r_row, $r_column + 1];
                }

                if ($r_row + 1 < $rows) {
                    $forbidden_places[] = [$r_row + 1, $r_column];
                }

                if ($r_column - 1 >= 0) {
                    $forbidden_places[] = [$r_row, $r_column - 1];
                }

                if ($r_row - 1 >= 0) {
                    $forbidden_places[] = [$r_row - 1, $r_column];
                }
            }

            $c = 0;
            foreach ($matrix as $i => $row) {
                foreach ($row as $k => $value) {
                    if ($value === null) {
                        $c++;
                        if ($c <= $num_places - $busy_places_count) {
                            $matrix[$i][$k] = $school;
                            $busy_places[] = [$i, $k];
                        }
                    }
                }
            }
        }

        return $matrix;
    }

    public function getLetter($num) {
        $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $result = "";

        while ($num > 0) {
            $remainder = ($num - 1) % 26;
            $result = $alphabet[$remainder] . $result;
            $num = (int)(($num - 1) / 26);
        }

        return $result;
    }


        // public function setMembersToPlaces($schools, $columns, $rows, $members) {
        //     $placesCounts = $this->getPlacesCountsInAuditorium($schools, $columns, $rows);
        //
        //     foreach ($placesCounts as $row => $rowSeats) {
        //         foreach ($rowSeats as $column => $seat) {
        //             if ($seat) {
        //                 $contestMemberId = array_pop($members[$seat])["id"];
        //                 $contestMember = ContestMember::findOrFail($contestMemberId);
        //
        //                 $letter = $this->getLetter($row + 1);
        //                 $seatNatural = $letter . strval($column + 1);
        //
        //                 $contestMember->seat = $seatNatural;
        //             }
        //
        //             $contestMember->save();
        //         }
        //     }
        //     return $placesCounts;
        // }


    public function setMembersToPlaces($schools, $columns, $rows, $members, $auditorium_id) {
        $placesCounts = $this->getPlacesCountsInAuditorium($schools, $columns, $rows);

        foreach ($placesCounts as $row => $rowSeats) {
            foreach ($rowSeats as $column => $seat) {
                $letter = $this->getLetter($row + 1);
                $seatNatural = $letter . strval($column + 1);

                if ($seat !== null) {

                    $contestMemberId = array_pop($members[$seat])["id"];
                    $contestMember = ContestMember::findOrFail($contestMemberId);
                    $contestMember->seat = $seatNatural;
                    $contestMember->auditorium_id = $auditorium_id;
                    $contestMember->save();
                }
            }
        }

        return $placesCounts;

    }

    // public function setMembersToPlaces($schools, $columns, $rows, $members) {
    //     $placesCounts = $this->getPlacesCountsInAuditorium($schools, $columns, $rows);
    //
    //     $res = [];
    //
    //     foreach ($placesCounts as $row => $rowSeats) {
    //         foreach ($rowSeats as $column => $seat) {
    //             $alphabet = "abcdefghijklmnopqrstuvwxyz";
    //             $letter = $this->getLetter($row + 1);
    //             $number = strval($column + 1);
    //
    //
    //             $res[$seatNatural] = $seat ? array_pop($members[$seat])["id"] : null;
    //         }
    //     }
    //
    //     dd("123", $placesCounts, $res);
    //
    //     return $placesCounts;
    // }

    public function getSchoolsByLevels($members) {
        $result = [];

        foreach ($members as $member) {
            list($school, $level) = $member;

            if (!isset($result[$level])) {
                $result[$level] = [$school => 1];
            } elseif (isset($result[$level][$school])) {
                $result[$level][$school]++;
            } else {
                $result[$level][$school] = 1;
            }
        }

        return $result;
    }

    public function getPlacesInAuditoriumsPattern($auditoriumsList, $schoolsByLevels) {
        $auditoriumsByLevels = [];

        foreach ($auditoriumsList as $auditorium => $details) {
            list($level, $places) = $details;

            if (isset($schoolsByLevels[$level])) {
                $data = array_fill_keys(array_keys($schoolsByLevels[$level]), 0);

                if (!isset($auditoriumsByLevels[$level])) {
                    $auditoriumsByLevels[$level] = [$auditorium => $data];
                } else {
                    $auditoriumsByLevels[$level][$auditorium] = $data;
                }
            }
        }

        return $auditoriumsByLevels;
    }

    public function getPlacesInAuditoriums($auditoriumsList, $schoolsByLevels) {
        $pattern = $this->getPlacesInAuditoriumsPattern($auditoriumsList, $schoolsByLevels);
        $notFilledPlacesCounts = [];

        foreach ($schoolsByLevels as $level => $schools) {
            $minHeap = new \SplPriorityQueue();

            foreach ($pattern[$level] as $auditorium => $data) {
                $minHeap->insert($auditorium, -array_sum($data));
            }

            foreach ($schools as $school => $schoolCount) {
                while ($schoolCount > 0 && !$minHeap->isEmpty()) {
                    $auditorium = $minHeap->extract();
                    $available_places = $auditoriumsList[$auditorium][1] - array_sum($pattern[$level][$auditorium]);

                    if ($available_places > 0) {
                        $to_allocate = min(1, $available_places);
                        $pattern[$level][$auditorium][$school] += $to_allocate;
                        $schoolCount -= $to_allocate;

                        $minHeap->insert($auditorium, -array_sum($pattern[$level][$auditorium]));
                    }
                }

                if ($schoolCount > 0) {
                    $notFilledPlacesCounts[$level][$school] = $schoolCount;
                }
            }
        }

        foreach ($notFilledPlacesCounts as $level => $schools) {
            foreach ($schools as $school => $restCount) {
                foreach ($pattern[$level] as $auditorium => &$data) {
                    if ($restCount == 0) break;

                    $available_places = $auditoriumsList[$auditorium][1] - array_sum($data);
                    if ($available_places > 0) {
                        $to_allocate = min($restCount, $available_places);
                        $data[$school] += $to_allocate;
                        $restCount -= $to_allocate;
                    }
                }
            }
        }

        return $pattern;
    }

    public function execute($auditoriums, $members)
    {
        $schoolsByLevels = $this->getSchoolsByLevels($members);
        $getPlacesInAuditoriums = $this->getPlacesInAuditoriums($auditoriums, $schoolsByLevels);

        return $getPlacesInAuditoriums;
    }
}
