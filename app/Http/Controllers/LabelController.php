<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LabelController extends Controller
{
    public function uploadForm()
    {
        return view('upload'); // Make sure you have an upload view
    }

    public function generateLabels(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        // Store the uploaded Excel file
        $filePath = $request->file('file')->store('uploads', 'local');

        // Load the uploaded Excel file
        $inputFilePath = storage_path('app/private/' . $filePath);
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($inputFilePath);
        $userdata = $spreadsheet->getActiveSheet()->toArray();

        // Load the template
        $templatePath = storage_path('app/private/templates/IRD_Tag_Rental.xlsx');
        $template = IOFactory::load($templatePath);
        $sheet = $template->getActiveSheet();

        // Determine rows per page and total pages
        $rowsPerPage = 18; // Number of rows in the template
        $labelsPerPage = 3; // Number of labels per page
        $columnsPerLabel = 4; // Width of each label in columns
        $totalPages = ceil(count($userdata) / $labelsPerPage);

        for ($page = 0; $page < $totalPages; $page++) {
            // Duplicate template rows for this page
            if ($page > 0) {
                $this->copyRowsWithImages($sheet, 1, $rowsPerPage, $page * $rowsPerPage +1);
            }

            // Populate labels for this page
            for ($label = 0; $label < $labelsPerPage; $label++) {
                $dataIndex = $page * $labelsPerPage + $label;
                if ($dataIndex >= count($userdata)) {
                    break; // Stop if no more data
                }

                $dataRow = $userdata[$dataIndex];
                $labelColumnOffset = $label * $columnsPerLabel; // Move horizontally to the correct column

                // Populate data into the label (adjust columns as needed)
                $sheet->setCellValue([1 + $labelColumnOffset, 1], $dataRow[3]); // First name
                $sheet->setCellValue([1 + $labelColumnOffset, 2], $dataRow[4]); // Last name
            }
        }

        // Save the generated file
        $outputFilePath = storage_path('app/' . time() . 'generated_labels.xlsx');
        $writer = new Xlsx($template);
        $writer->save($outputFilePath);

        // Return the file as a download
        return response()->download($outputFilePath)->deleteFileAfterSend(true);
    }

    private function copyRowsWithImages(Worksheet $sheet, int $startRow, int $endRow, int $destinationRow)
    {
        for ($row = $startRow; $row <= $endRow; $row++) {
            $destinationOffset = $destinationRow + ($row - $startRow);

            // Copy the row height
            $sourceHeight = $sheet->getRowDimension($row)->getRowHeight();
            $sheet->getRowDimension($destinationOffset)->setRowHeight($sourceHeight);

            // Copy the content and styles for each cell in the row
            foreach ($sheet->getColumnIterator() as $column) {
                $cell = $sheet->getCell($column->getColumnIndex() . $row);
                $destinationCell = $sheet->getCell($column->getColumnIndex() . $destinationOffset);

                // Copy cell value
                $destinationCell->setValue($cell->getValue());

                // Copy cell styles
                $sheet->duplicateStyle(
                    $sheet->getStyle($column->getColumnIndex() . $row),
                    $column->getColumnIndex() . $destinationOffset
                );
            }
        }

        // Copy images for the range
        $this->copyImages($sheet, $startRow, $endRow, $destinationRow);
    }

    private function copyImages(Worksheet $sheet, int $startRow, int $endRow, int $destinationRow)
    {
        // Define the path to the logo image
        $logoPath = storage_path('app/public/images/bedloft_logo_stacked.png');

        // Determine the base rows for the images
        $imagesBaseRow = 18; // Row where the images are placed in the template
        $rowsPerPage = 18; // Total rows per page

        // Columns for each label's image
        $labelColumns = ['A', 'E', 'I'];

        // Define cell dimensions (adjust based on your template layout)
        $cellWidth = 482;
        $cellHeight = 104;

        // Define the image dimensions (adjust based on your template layout)
        $imageWidth = 110;
        $imageHeight = 60;

        foreach ($labelColumns as $index => $column) {
            // Calculate the row for the current page
            $imageRow = $destinationRow + ($imagesBaseRow - $startRow);

            // Calculate offsets to center the image
            $offsetX = max(0, ($cellWidth - $cellHeight) / 2); // Center horizontally
            $offsetY = max(0, ($cellHeight - $imageHeight) / 2); // Center vertically

            // Create a new drawing for the logo
            $newDrawing = new Drawing();
            $newDrawing->setName('Logo');
            $newDrawing->setDescription('Copied Logo');
            $newDrawing->setPath($logoPath); // Reference the uploaded image
            $newDrawing->setCoordinates($column . $imageRow);
            $newDrawing->setWidth($imageWidth);
            $newDrawing->setHeight($imageHeight);
            $newDrawing->getOffsetX($offsetX);
            $newDrawing->getOffsetY($offsetY);
            $newDrawing->setWorksheet($sheet);
        }
    }
}

