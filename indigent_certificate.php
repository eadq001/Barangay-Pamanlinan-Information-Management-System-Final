<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barangay Indigent Certificate</title>
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">

<style>
    body {
        font-family: "Times New Roman", serif;
        background: #f4f7f8;
        margin: 0;
        padding: 0;
    }

    .top-btn {
        text-align: left;
        margin: 20px;
    }

    .top-btn button {
        background: #607d8b;
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .top-btn button:hover {
        background: #455a64;
    }

    .certificate-container {
        width: 8.27in;
        min-height: 11.69in;
        background: #fff;
        margin: 0 auto 20px auto;
        padding: 1in;
        position: relative;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        border: 1px solid #ccc;
    }

    .watermark {
        position: absolute;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 75%;
        opacity: 0.08;
        z-index: 0;
        pointer-events: none;
    }

    .content {
        position: relative;
        z-index: 2;
    }

    .header {
        text-align: center;
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .header img {
        position: absolute;
    }

    .seal-left {
        top: 1.1in;
        left: 0.8in;
        width: 100px;
    }

    .logo-right {
        top: 1.1in;
        right: 0.8in;
        width: 120px;
    }

    h3, h4, h2, p {
        margin: 0;
    }

    h2 {
        margin-top: 10px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    h4 {
        font-weight: normal;
    }

    .body-text {
        margin-top: 30px;
        line-height: 1.6;
        font-size: 17px;
        text-align: justify;
    }

    input[type="text"], input[type="date"] {
        border: none;
        border-bottom: 1px solid #000;
        font-family: "Times New Roman", serif;
        font-size: 16px;
        background: transparent;
        text-align: center;
        outline: none;
    }

    .signature {
        margin-top: 60px;
        text-align: right;
        font-weight: bold;
    }

    .signature em {
        font-weight: normal;
    }

    .footer {
        margin-top: 40px;
        font-size: 14px;
        font-style: italic;
    }

    .print-btn {
        text-align: center;
        margin-bottom: 30px;
    }

    .print-btn button {
        background: #004d40;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .print-btn button:hover {
        background: #00796b;
    }

    @media print {
        .top-btn, .print-btn {
            display: none;
        }
        .certificate-container {
            box-shadow: none;
            border: none;
            margin: 0;
        }
    }
</style>
</head>
<body>

<!-- ✅ Back to Dashboard -->
<div class="top-btn">
    <button onclick="window.location.href='pamanlinan.php'">⬅ Back to Dashboard</button>
</div>

<!-- ✅ Certificate Layout -->
<div class="certificate-container">
    <img src="pamanlinan.png" class="watermark" alt="Barangay Seal">
    <div class="content">
        <div class="header">
            <img src="pamanlinan.png" class="seal-left" alt="Barangay Seal">
            <img src="Bagong-Pilipinas-Logo-1966x2048.png" class="logo-right" alt="Bagong Pilipinas Logo">

            <h4>Republic of the Philippines</h4>
            <h4>Province of Surigao del Sur</h4>
            <h4>CITY OF BISLIG</h4>
            <h4><strong>Barangay Pamanlinan</strong></h4>
            <br>
            <h4>OFFICE OF THE PUNONG BARANGAY</h4>
            <br>
            <h2>BARANGAY CERTIFICATION</h2>
            <h4><strong>(INDIGENT CERTIFICATE)</strong></h4>
        </div>

        <div class="body-text">
            <p><strong>TO WHOM IT MAY CONCERN;</strong></p>
            <br>
            <p>THIS IS TO CERTIFY that <input type="text" style="width:250px;">
            of legal age, <input type="text" style="width:100px;" placeholder="Status">,
            Filipino and presently residing at Purok <input type="text" style="width:80px;">,
            Barangay Pamanlinan, Bislig City, Surigao del Sur,
            <strong>belongs to indigent family.</strong></p>

            <br>
            <p>THIS FURTHER CERTIFIES that <input type="text" style="width:400px;"> is
            <input type="text" style="width:250px;"></p>

            <br>
            <p>THIS CERTIFICATION is being issued upon the request of the aforementioned individual
            for the purpose of <input type="text" style="width:300px;"> and for any other legal purpose it may serve.</p>

            <br>
            <p>ISSUED this <input type="text" style="width:50px;"> day of 
            <input type="text" style="width:150px;"> 2025 at Barangay Pamanlinan, Bislig City,
            and Surigao del Sur.</p>
        </div>

        <div class="signature">
            <p>__________________________<br>
            JENNIFER M. MAGNO<br>
            <em>Punong Barangay</em></p>
        </div>

        <div class="footer">
            <p>Not valid without seal</p>
        </div>
    </div>
</div>

<!-- ✅ Print Button -->
<div class="print-btn">
    <button onclick="window.print()">🖨 Print Certificate</button>
</div>

</body>
</html>
