<?php
include '../database/db.php'; 
require '../authentication/authentication.php'; 
require '../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

checkAuth('admin');

$sql = "SELECT er.*, e.nama AS event_name 
        FROM event_registrations er 
        JOIN events e ON er.event_id = e.id";

$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'User ID');
$sheet->setCellValue('B1', 'Username');
$sheet->setCellValue('C1', 'Email');
$sheet->setCellValue('D1', 'Event ID');
$sheet->setCellValue('E1', 'Nama Event');
$sheet->setCellValue('F1', 'Tanggal Registrasi');

$row = 2; 
while ($data = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $data['user_id']);
    $sheet->setCellValue('B' . $row, $data['username']);
    $sheet->setCellValue('C' . $row, $data['email_user']);
    $sheet->setCellValue('D' . $row, $data['event_id']);
    $sheet->setCellValue('E' . $row, $data['event_name']);
    $sheet->setCellValue('F' . $row, $data['registration_date']);
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="registrants_data.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;