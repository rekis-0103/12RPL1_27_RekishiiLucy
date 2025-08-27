<?php
session_start();
require('../vendor/setasign/fpdf/fpdf.php');
include '../connect/koneksi.php';

// --- Ambil data filter
$status = $_GET['status'] ?? '';
$filter = "";
if (!empty($status) && $status !== 'semua') {
    $filter = "WHERE a.status = '" . mysqli_real_escape_string($conn, $status) . "'";
}

$sql = "SELECT u.full_name AS nama, l.title AS nama_pekerjaan, l.location AS tempat, 
               a.status, a.applied_at AS tanggal_lamar
        FROM applications a
        JOIN users u ON u.user_id = a.user_id
        JOIN lowongan l ON l.job_id = a.job_id
        $filter
        ORDER BY a.applied_at DESC";
$result = mysqli_query($conn, $sql);

// --- Data sesi
$printedBy = $_SESSION['full_name'] ?? 'Administrator';

// --- Custom PDF
class PDF extends FPDF {
    function Header() {
        // Logo - diperbesar ukurannya
        $this->Image('../assets/logo.png', 15, 13, 45); 
        // Nama Perusahaan
        $this->SetFont('Arial','B',14);
        $this->Cell(0,7,'PT Waindo Specterra',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,7,'Kompleks Perkantoran Pejaten Raya #7-8',0,1,'C');
        $this->Cell(0,7,'Jl. Pejaten Raya No.2 Jakarta Selatan 12510',0,1,'C');
        // Garis
        $this->Line(10,35,200,35);
        $this->Ln(15);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// Judul Laporan
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,7,'Laporan Rekapitulasi Pelamar',0,1,'C');
$pdf->Ln(3);

// Nama pencetak
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,7,'Dicetak oleh : '.$printedBy,0,1,'L');

// Status filter
$labelStatus = empty($status) || $status === 'semua' ? 'Semua' : $status;
$pdf->Cell(0,7,'Status : '.$labelStatus,0,1,'L');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial','B',9);
$pdf->Cell(10,10,'No',1,0,'C');
$pdf->Cell(40,10,'Nama',1,0,'C');
$pdf->Cell(45,10,'Nama Pekerjaan',1,0,'C');
$pdf->Cell(35,10,'Tempat',1,0,'C');
$pdf->Cell(35,10,'Status',1,0,'C');
$pdf->Cell(25,10,'Tanggal Lamar',1,1,'C');

// Isi tabel
$pdf->SetFont('Arial','',9);
$no=1;
while($row = mysqli_fetch_assoc($result)){
    $tanggal = date('d-m-Y', strtotime($row['tanggal_lamar']));
    $pdf->Cell(10,10,$no++,1,0,'C');
    $pdf->Cell(40,10,$row['nama'],1, 0, 'C');
    $pdf->Cell(45,10,$row['nama_pekerjaan'],1, 0, 'C');
    $pdf->Cell(35,10,$row['tempat'],1, 0, 'C');
    $pdf->Cell(35,10,$row['status'],1, 0, 'C');
    $pdf->Cell(25,10,$tanggal,1,1, 'C');
}

// Bagian tanda tangan
$pdf->Ln(15);
$pdf->SetFont('Arial','',10);
$bulanIndo = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
    7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
];
$tanggalCetak = date('d');
$bulanCetak = $bulanIndo[(int)date('m')];
$tahunCetak = date('Y');

// Posisikan di kanan untuk tanggal
$pdf->Cell(0,7,'Jakarta, '.$tanggalCetak.' '.$bulanCetak.' '.$tahunCetak,0,1,'R');
$pdf->Ln(15); // Spasi sebelum garis tanda tangan

// Tambahkan garis untuk tanda tangan (di kanan)
$pdf->SetX(160); // Posisi garis di kanan
$pdf->Cell(40, 0.5, '', 'B', 1, 'C'); // Garis sepanjang 50mm

// Spasi setelah garis
$pdf->Ln(5);

$pdf->SetX(160); // Reset posisi X
$pdf->Cell(0, 7, $printedBy, 0, 1, 'C'); 
$pdf->Output();