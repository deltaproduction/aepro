<?php
function seats($counts, $columns, $rows) {
    $matrix = array_fill(0, $rows, array_fill(0, $columns, null));
    $all_places = [];

    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $columns; $j++) {
            $all_places[] = [$i, $j];
        }
    }

    usort($counts, function($a, $b) {
        return $b[1] - $a[1];
    });

    $busy_places = [];

    foreach ($counts as $count) {
        $school = $count[0];
        $num_places = $count[1];
        $forbidden_places = [];
        $busy_places_count = 0;

        for ($i = 0; $i < $num_places; $i++) {
            $f_places = array_merge($busy_places, $forbidden_places);

            // Filter all_places to get the free places
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

$seating = seats([
    ["a", 8],
    ["b", 2],
    ["c", 6],
], 4, 4);

foreach ($seating as $row) {
    echo implode(" ", array_map(function($item) {
        return $item === null ? '.' : $item;
    }, $row)) . "\n";
}
