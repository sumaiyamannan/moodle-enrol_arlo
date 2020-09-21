<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Carl Anderson <carl.anderson@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_reportbuilder\local\graph;

/**
 * Abstraction for ChartJS library for use with reports.
 */
final class chartjs extends base {
    /** @var array ChartJS settings */
    protected $chartsettings;
    /** @var array list of chart labels */
    protected $labels;
    /** @var  array list of chart colours */
    protected $colors;
    /** @var  array list of supported chart types */
    protected static $allowed_types = [
        'column',
        'line',
        'bar',
        'pie',
        'scatter',
        'area',
        'doughnut',
        'progress'
    ];

    protected function init(): void {
        parent::init();

        // We turn off most of the responsive settings in progress charts so that
        // we can more easily control the graph layouts with css
        $this->chartsettings = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'legend' => [
                'display' => $this->graphrecord->type !== 'progress',
            ]
        ];

        if (!$this->is_pie_chart()) {
            $this->chartsettings['scales'] = [
                'xAxes' => [
                    [
                        'stacked' => !empty($this->graphrecord->stacked),
                        'ticks' => [
                            'beginAtZero' => true,
                        ],
                        'gridLines' => [
                            'display' => $this->graphrecord->type === 'bar',
                        ],
                    ]
                ],
                'yAxes' => [
                    [
                        'stacked' => !empty($this->graphrecord->stacked),
                        'ticks' => [
                            'beginAtZero' => true,
                        ],
                        'gridLines' => [
                            'display' => $this->graphrecord->type !== 'bar',
                        ],
                    ]
                ]
            ];
        } else {
            $this->chartsettings['cutoutPercentage'] = chartjs::convert_type($this->graphrecord->type) === 'doughnut' ? 66 : 0;
        }

        $this->labels = [];
        $this->colors = [
            '#3869B1',
            '#DA7E31',
            '#3F9852',
            '#CC2428',
            '#958C3D',
            '#6B4C9A',
            '#8C8C8C',
        ];

        //Create an array entry for each series
        foreach ($this->series as $k => $item) {
            if (empty($item)) {
                continue;
            }

            $this->values[] = [
                'label' => $this->report->format_column_heading($this->report->columns[$item], true),
                'fill' => $this->graphrecord->type === 'area',
                'showLine' => $this->graphrecord->type !== 'scatter',
                'data' => [],
                'backgroundColor' => [], //for pie charts -- will be overridden further down
            ];
        }

        if (!$this->is_pie_chart()) {
            foreach ($this->values as $k => $val) {
                $this->values[$k]['backgroundColor'] = $this->colors[$k % count($this->colors)];

                if ($this->graphrecord->type === 'line') {
                    $this->values[$k]['borderColor'] = $this->colors[$k % count($this->colors)];
                }
            }
        }

        if (!empty($this->usersettings)) {
            $this->usersettings = settings\chartjs::create($this->usersettings);
        } else {
            $this->usersettings = [];
        }
    }

    protected function process_data(array $data): void {
        $values = [];
        if ($this->category == base::GRAPH_CATEGORY_SIMPLE) {
            $label = $this->processedcount + 1;
        } else {
            $label = $data[$this->category];
        }
        $this->labels[] = $label;

        foreach ($this->series as $i => $key) {
            $val = $data[$i];
            $values[] = $val;
        }

        foreach ($values as $k => $val) {
            // Ensure val is a numeric data type
            $val = rtrim($val, "%");
            if ($val === '' or !is_numeric($val)) {
                // There is no way to plot non-numeric data
                $val = null;
            } else if (is_string($val)) {
                if ($val === (string)(int)$val) {
                    $val = (int)$val;
                } else {
                    $val = (float)$val;
                }
            }

            $this->values[$k]['total'] = isset($this->values[$k]['total']) ? $this->values[$k]['total'] + $val : $val;

            if ($this->graphrecord->type === 'scatter') {
                $this->values[$k]['data'][] = ['x' => $label, 'y' => $val];
            } else {
                $this->values[$k]['data'][] = $val;
            }

            if ($this->is_pie_chart()) {
                $this->values[$k]['backgroundColor'][] = $this->colors[$this->processedcount % count($this->colors)];
            }

            if (empty($this->graphrecord->stacked)) {
                $this->values[$k]['stack'] = $k;
            }
        }

        $this->processedcount++;
    }

    /**
     * Render chart.
     *
     * @param int|null $width
     * @param int|null $height
     * @return string HTML markup
     */
    public function render(int $width = null, int $height = null): string {
        $data = $this->get_render_data($width, $height);
        return self::render_data($data);
    }

    /**
     * Returns cache data for future rendering.
     *
     * @param int|null $width width of the graph
     * @param int|null $height height of the graph
     * @return array data for rendering, must be compatible with json_encode
     */
    public function get_render_data(?int $width, ?int $height): array {
        // Progress charts need to draw multiple charts, so they have to be handled separately.
        if ($this->graphrecord->type === 'progress') {
            $context = $this->get_progress_chart_data($width, $height);
        } else {
            if ($this->is_pie_chart()) {
                $this->fix_pie_colors();
            }

            // Since we're using the ChartJS responsive setting, we ignore $width so it will grow correctly
            $context = [
                'width' => (empty($width) ? null : $width),
                'height' => (empty($height) ? null : $height),
                'chart' => [
                    'settings' => json_encode([
                        'type' => chartjs::convert_type($this->graphrecord->type),
                        'data' => [
                            'labels' => $this->labels,
                            'datasets' => $this->values,
                        ],
                        'options' => settings\chartjs::merge($this->chartsettings, $this->usersettings),
                    ])
                ]
            ];
        }

        return $context;
    }

    /**
     * Returns the rendered graph markup.
     *
     * @param array data from get_render_data()
     * @return string
     */
    public static function render_data(array $data): string {
        global $OUTPUT;
        return $OUTPUT->render_from_template('totara_reportbuilder/chartjs', $data);
    }

    /**
     * Generate the context data for the progress donut chart type.
     *
     * @param int|null $width Minimal width
     * @param int|null $height Minimal height
     * @return array context data for a progress donut chart
     */
    private function get_progress_chart_data(int $width = null, int $height = null): array {

        $charts = [];

        // For progress charts, we have to reprocess them because we need to know the total of the entire dataset
        // before we can split these out into their own charts
        foreach ($this->values as $i => $dataset) {
            $data = $dataset['data'];

            foreach ($data as $k => $val) {
                $chart = $dataset; // clone default settings

                $chart['data'] = [$val, $dataset['total'] - $val];
                $chart['backgroundColor'] = [$this->colors[0], '#8C8C8C'];

                $settings = $this->chartsettings;
                $settings['title'] = [
                    'display' => true,
                    'text' => $this->labels[$k],
                ];
                $settings['plugins'] = [
                    'doughnutlabel' => [
                        'labels' => [
                            [
                                'text' => round($val / $chart['total'] * 100, 0) . '%',
                                'font' => [
                                    'size' => 100 // this doesn't reflect actual size, but a ratio -- it get scaled down
                                ]
                            ],
                            [
                                'text' => ''.$val,
                                'font' => [
                                    'size' => 66
                                ]
                            ]
                        ]
                    ]
                ];

                $charts[] = [
                    'settings' => json_encode([
                        'aspectRatio' => 1,
                        'type' => chartjs::convert_type($this->graphrecord->type),
                        'data' => [
                            'labels' => [$this->labels[$k], ''],
                            'datasets' => [$chart],
                        ],
                        'options' => $settings
                    ])
                ];
            }
        }

        $context = [
            'width' => (empty($width) ? null : $width),
            'height' => (empty($height) ? null : $height),
            'type' => $this->graphrecord->type,
            'chart' => $charts,
        ];

        return $context;
    }

    /**
     * Fixes pie charts so that they don't have two of the same colour next to each other
     */
    private function fix_pie_colors() {
        $k = $this->processedcount - 1;

        if ($this->processedcount % count($this->colors) === 1) {
            // Pie charts only have a single series, so we only need to target the first one
            $this->values[0]['backgroundColor'][$k] = $this->colors[($k + 1) % count($this->colors)];
        }
    }

    protected static function convert_type($type) {
        switch ($type) {
            case 'column':
                return 'bar';
            case 'bar':
                return 'horizontalBar';
            case 'area':
                return 'line';
            case 'progress':
                return 'doughnut';
            default:
                return $type;
        }
    }

    /**
     * Returns whether this is one of the pie chart variants
     * @return bool
     */
    private function is_pie_chart() {
        return $this->graphrecord->type === 'pie'
            || $this->graphrecord->type === 'doughnut'
            || $this->graphrecord->type === 'progress';
    }

    public static function get_name(): string {
        return get_string('graphlibchartjs', 'totara_reportbuilder');
    }
}
