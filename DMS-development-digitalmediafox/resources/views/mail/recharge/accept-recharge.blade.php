<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recharge Accepted - Speed Logistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px 24px;
            border: 1px solid #e2e2e2;
            border-top: 3px solid #722c81;
            border-radius: 6px;
        }

        h2 {
            font-size: 20px;
            margin-bottom: 12px;
            color: #722c81;
        }

        p {
            line-height: 1.5;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }

        .details-table th,
        .details-table td {
            border: 1px solid #dddddd;
            padding: 10px;
            text-align: left;
        }

        .details-table th {
            width: 30%;
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
            font-size: 12px;
            color: #777;
            text-align: center;
            padding-top: 12px;
            border-top: 1px solid #e2e2e2;
        }
    </style>
</head>
<body>

    <div class="email-container">

        <h2>Recharge Accepted</h2>
        <p>
            A recharge request has been accepted from <strong>{{ $opMangerData->name ?? 'Operation Manager' }}</strong>.
            Please review the details below and proceed accordingly.
        </p>

        <table class="details-table">
            <tr>
                <th>Driver Name</th>
                <td>{{ $driverData->name ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>Branch</th>
                <td>{{ $driverData->branch->name ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>Iqaama Number</th>
                <td>{{ $driverData->iqaama_number ?? 'N/A' }}</td>
            </tr>
            
            <tr>
                <th>Phone No</th>
               <td>{{ $requestRecharge->mobile ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>Opearator</th>
               <td>{{ $requestRecharge->opearator ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>Accepted By</th>
               <td>{{ $opMangerData->name ?? 'N/A' }} ({{ $opMangerData->role->name ?? 'N/A' }})</td>
            </tr>

        </table>

        <p>
            For more details, please check your Recharge dashboard.
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} Speed Logistics — All rights reserved.
        </div>

    </div>

</body>
</html>
