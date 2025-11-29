<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recharge Request Rejected - Speed Logistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f7f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            padding: 22px 26px;
            border: 1px solid #e2e2e2;
            border-top: 3px solid #722c81;
            border-radius: 6px;
        }

        h2 {
            font-size: 20px;
            margin-bottom: 12px;
            color: #b30000;
        }

        p {
            font-size: 14px;
            line-height: 1.5;
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

        .reason-box {
            margin-top: 18px;
            background: #fff3f3;
            border-left: 4px solid #d9534f;
            padding: 12px 14px;
            font-size: 14px;
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

        <h2>Recharge Rejected</h2>

        <p>
            A recharge request has been rejected from <strong>{{ $opMangerData->name ?? 'Operation Manager' }}</strong>.
            Please review the details below:.
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
                <th>Rejected By</th>
               <td>{{ $opMangerData->name ?? 'N/A' }} ({{ $opMangerData->role->name ?? 'N/A' }})</td>
            </tr>

        </table>

        

        <!-- Rejection Reason -->
        <div class="reason-box">
            <strong>Rejection Reason:</strong>
            <p style="margin-top: 6px;">
                {{ $rejectReason }}
            </p>
        </div>

        <p>
            Please review the reason above and resubmit the request if needed.
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} Speed Logistics — All rights reserved.
        </div>

    </div>

</body>
</html>
