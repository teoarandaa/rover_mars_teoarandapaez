<?php

namespace App\Models;

class Rover
{
    public $x;
    public $y;
    public $direction;
    public $obstacles;

    private const DIRECTIONS = ['N', 'E', 'S', 'W'];

    public function __construct($x, $y, $direction, $obstacles = [])
    {
        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;
        $this->obstacles = $obstacles;
    }

    public function execute(string $commands): array
    {
        foreach (str_split(strtolower($commands)) as $command) {
            if ($command === 'f') {
                if (!$this->moveForward()) {
                    return ['status' => 'obstacle', 'x' => $this->x, 'y' => $this->y];
                }
            } elseif ($command === 'l') {
                $this->turnLeft();
            } elseif ($command === 'r') {
                $this->turnRight();
            }
        }

        return ['status' => 'ok', 'x' => $this->x, 'y' => $this->y];
    }

    private function turnLeft()
    {
        $idx = array_search($this->direction, self::DIRECTIONS);
        $this->direction = self::DIRECTIONS[($idx + 3) % 4]; // -1 modulo 4
    }

    private function turnRight()
    {
        $idx = array_search($this->direction, self::DIRECTIONS);
        $this->direction = self::DIRECTIONS[($idx + 1) % 4];
    }

    private function moveForward(): bool
    {
        $nextX = $this->x;
        $nextY = $this->y;

        switch ($this->direction) {
            case 'N': $nextY++; break;
            case 'E': $nextX++; break;
            case 'S': $nextY--; break;
            case 'W': $nextX--; break;
        }

        // Bucle del mundo (200x200)
        $nextX = ($nextX + 200) % 200;
        $nextY = ($nextY + 200) % 200;

        // Detección de obstáculos
        if (in_array([$nextX, $nextY], $this->obstacles)) {
            return false;
        }

        $this->x = $nextX;
        $this->y = $nextY;
        return true;
    }
} 