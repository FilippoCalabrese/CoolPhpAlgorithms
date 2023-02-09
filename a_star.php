<?php

function a_star($graph, $start, $goal) {
    $open_list = [];
    $closed_list = [];
    $came_from = [];
    $g_score = [];
    $f_score = [];

    $open_list[] = $start;
    $g_score[$start] = 0;
    $f_score[$start] = heuristic_cost_estimate($start, $goal);

    while (!empty($open_list)) {
        $current = get_node_with_lowest_f_score($open_list, $f_score);
        if ($current === $goal) {
            return reconstruct_path($came_from, $goal);
        }

        unset($open_list[array_search($current, $open_list)]);
        $closed_list[] = $current;

        foreach ($graph[$current] as $neighbour => $cost) {
            if (in_array($neighbour, $closed_list)) {
                continue;
            }

            $tentative_g_score = $g_score[$current] + $cost;
            if (!in_array($neighbour, $open_list) || $tentative_g_score < $g_score[$neighbour]) {
                $came_from[$neighbour] = $current;
                $g_score[$neighbour] = $tentative_g_score;
                $f_score[$neighbour] = $g_score[$neighbour] + heuristic_cost_estimate($neighbour, $goal);
                if (!in_array($neighbour, $open_list)) {
                    $open_list[] = $neighbour;
                }
            }
        }
    }

    return [];
}

function heuristic_cost_estimate($start, $goal) {
    $dx = (int)$start[0] - (int)$goal[0];
    $dy = (int)$start[1] - (int)$goal[1];
    return sqrt($dx * $dx + $dy * $dy);
}

function get_node_with_lowest_f_score($nodes, $f_score) {
    $lowest_f_score = INF;
    $lowest_f_score_node = null;
    foreach ($nodes as $node) {
        if ($f_score[$node] < $lowest_f_score) {
            $lowest_f_score = $f_score[$node];
            $lowest_f_score_node = $node;
        }
    }
    return $lowest_f_score_node;
}

function reconstruct_path($came_from, $current_node) {
    $total_path = [$current_node];
    while (array_key_exists($current_node, $came_from)) {
        $current_node = $came_from[$current_node];
        $total_path[] = $current_node;
    }
    return array_reverse($total_path);
}

/**
 * Add a new node to the graph and update the references in its neighboring nodes
 *
 * @param array $graph The graph represented as an adjacency list
 * @param string $newNode The name of the new node to be added to the graph
 * @param array $neighbors An array of neighboring nodes and their weights
 * @throws Exception If the new node already exists in the graph, or if the format of the neighboring nodes and weights is incorrect
 * @return array The updated graph with the new node added
 */
function addNodeToGraph(array $graph, string $newNode, array $neighbors)
{
    if (array_key_exists($newNode, $graph)) {
        throw new Exception("Error: the node already exists in the graph.");
    }

    if (empty($neighbors)) {
        $graph[$newNode] = [];
        return $graph;
    }

    foreach ($neighbors as $neighbor => $weight) {
        if (!array_key_exists($neighbor, $graph)) {
            throw new Exception("Error: the neighboring node does not exist in the graph.");
        }
        if (!is_numeric($weight)) {
            throw new Exception("Error: the weight of the edge is not a number.");
        }
    }

    $graph[$newNode] = $neighbors;
    foreach ($neighbors as $neighbor => $weight) {
        $graph[$neighbor][$newNode] = $weight;
    }

    return $graph;
}


$graph = [
    "Roma" => ["Milano" => 350, "Napoli" => 200, "Firenze" => 250],
    "Milano" => ["Roma" => 350, "Napoli" => 600, "Firenze" => 200, "Torino" => 100],
    "Napoli" => ["Roma" => 200, "Milano" => 600, "Firenze" => 450, "Torino" => 550],
    "Firenze" => ["Roma" => 250, "Milano" => 200, "Napoli" => 450],
    "Torino" => ["Milano" => 100, "Napoli" => 550]
];

//example call to a_star function
$path = a_star($graph, "Torino", "Firenze");
print_r($path);
