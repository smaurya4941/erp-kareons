<?php

namespace App\Services;

class ExportService
{
    /**
     * Generate a CSV response from an Eloquent query/collection
     *
     * @param \Illuminate\Support\Collection $data
     * @param array $columns associative array of 'column_header' => 'field_name' or callback
     * @param string $filename
     */
    public function downloadCsv($data, array $columns, string $filename)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write headers
            fputcsv($file, array_keys($columns));

            // Write data rows
            foreach ($data as $row) {
                $rowData = [];
                foreach ($columns as $header => $field) {
                    if (is_callable($field)) {
                        $rowData[] = $field($row);
                    } else {
                        $rowData[] = data_get($row, $field, '');
                    }
                }
                fputcsv($file, $rowData);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
