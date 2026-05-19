<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->name }} - {{ $payroll->month }}</title>
    <!-- Google Fonts: Inter for clean formal corporate look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .actions-toolbar {
            max-width: 800px;
            margin: 0 auto 30px auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #0f172a;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.15s ease;
        }

        .btn:hover {
            background-color: #1e293b;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        /* Traditional Payslip Design */
        .payslip-card {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #94a3b8;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border-radius: 2px;
        }

        .payslip-header {
            text-align: center;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .company-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .company-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        .slip-title {
            font-size: 15px;
            font-weight: 700;
            margin-top: 15px;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Information Grid */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .info-table td {
            padding: 8px 12px;
            font-size: 12px;
            border: 1px solid #cbd5e1;
        }

        .info-label {
            font-weight: 600;
            color: #475569;
            background-color: #f8fafc;
            width: 20%;
        }

        .info-val {
            color: #0f172a;
            font-weight: 500;
            width: 30%;
        }

        /* Salary Breakdown Table */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .salary-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 10px 15px;
            text-align: left;
            border: 1px solid #1e293b;
        }

        .salary-table td {
            padding: 10px 15px;
            font-size: 12px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }

        .sub-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sub-table td {
            border: none;
            padding: 6px 0;
        }

        .sub-table tr:not(:last-child) td {
            border-bottom: 1px dashed #e2e8f0;
        }

        .amount-col {
            text-align: right;
            font-weight: 600;
        }

        .total-row {
            font-weight: 700;
            background-color: #f1f5f9;
        }

        .total-row td {
            border-top: 2px solid #1e293b;
        }

        /* Net Pay Summary Panel */
        .summary-panel {
            border: 1px solid #94a3b8;
            background-color: #f8fafc;
            padding: 20px;
            margin-bottom: 35px;
            border-radius: 2px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .net-pay-title {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .net-pay-amount {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
        }

        .net-pay-words {
            font-size: 11px;
            font-style: italic;
            color: #64748b;
            margin-top: 5px;
            font-weight: 500;
        }

        /* Footer Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            padding-top: 40px;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .sig-block {
            text-align: center;
            width: 220px;
        }

        .sig-line {
            border-top: 1px dotted #1e293b;
            margin-bottom: 8px;
        }

        .sig-label {
            font-size: 10px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .verified-stamp {
            border: 1.5px solid #059669;
            color: #059669;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 10px;
            display: inline-block;
            letter-spacing: 1px;
            margin-top: 15px;
            transform: rotate(-3deg);
            border-radius: 2px;
        }

        /* Print Override */
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
                margin: 0;
            }
            .actions-toolbar {
                display: none !important;
            }
            .payslip-card {
                box-shadow: none !important;
                border: 1px solid #1e293b !important;
                max-width: 100% !important;
                padding: 20px !important;
            }
        }
    </style>
</head>
<body>

    @php
        $currencySymbol = cache()->remember("company_currency_" . $payroll->employee->company_name, 3600, function() use ($payroll) {
            return \App\Models\User::where('role', 'admin')
                ->where('company_name', $payroll->employee->company_name)
                ->value('currency') ?? '$';
        });

        // Traditional Number-to-Words Converter for Corporate Payslip
        if (!function_exists('payslipNumberToWords')) {
            function payslipNumberToWords($number) {
                $hyphen      = '-';
                $conjunction = ' and ';
                $separator   = ', ';
                $negative    = 'negative ';
                $decimal     = ' point ';
                $dictionary  = array(
                    0                   => 'zero',
                    1                   => 'one',
                    2                   => 'two',
                    3                   => 'three',
                    4                   => 'four',
                    5                   => 'five',
                    6                   => 'six',
                    7                   => 'seven',
                    8                   => 'eight',
                    9                   => 'nine',
                    10                  => 'ten',
                    11                  => 'eleven',
                    12                  => 'twelve',
                    13                  => 'thirteen',
                    14                  => 'fourteen',
                    15                  => 'fifteen',
                    16                  => 'sixteen',
                    17                  => 'seventeen',
                    18                  => 'eighteen',
                    19                  => 'nineteen',
                    20                  => 'twenty',
                    30                  => 'thirty',
                    40                  => 'forty',
                    50                  => 'fifty',
                    60                  => 'sixty',
                    70                  => 'seventy',
                    80                  => 'eighty',
                    90                  => 'ninety',
                    100                 => 'hundred',
                    1000                => 'thousand',
                    1000000             => 'million'
                );

                if (!is_numeric($number)) {
                    return false;
                }

                if ($number < 0) {
                    return $negative . payslipNumberToWords(abs($number));
                }

                $string = null;
                $fraction = null;

                if (strpos($number, '.') !== false) {
                    list($number, $fraction) = explode('.', $number);
                }

                switch (true) {
                    case $number < 21:
                        $string = $dictionary[$number];
                        break;
                    case $number < 100:
                        $tens   = ((int) ($number / 10)) * 10;
                        $units  = $number % 10;
                        $string = $dictionary[$tens];
                        if ($units) {
                            $string .= $hyphen . $dictionary[$units];
                        }
                        break;
                    case $number < 1000:
                        $hundreds  = $number / 100;
                        $remainder = $number % 100;
                        $string = $dictionary[(int)$hundreds] . ' ' . $dictionary[100];
                        if ($remainder) {
                            $string .= $conjunction . payslipNumberToWords($remainder);
                        }
                        break;
                    default:
                        $baseUnit = pow(1000, floor(log($number, 1000)));
                        $numBaseUnits = (int) ($number / $baseUnit);
                        $remainder = $number % $baseUnit;
                        $string = payslipNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                        if ($remainder) {
                            $string .= $remainder < 100 ? $conjunction : $separator;
                            $string .= payslipNumberToWords($remainder);
                        }
                        break;
                }

                if (null !== $fraction && is_numeric($fraction) && (int)$fraction > 0) {
                    $string .= $decimal;
                    $words = array();
                    foreach (str_split((string) $fraction) as $number) {
                        $words[] = $dictionary[$number];
                    }
                    $string .= implode(' ', $words);
                }

                return ucfirst($string);
            }
        }

        $netSalaryInWords = payslipNumberToWords((int)$payroll->net_salary);
    @endphp

    <!-- Floating Actions Toolbar (Hidden on Print) -->
    <div class="actions-toolbar">
        <div style="font-size: 13px; font-weight: 500; color: #475569;">
            <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Verified & Approved Disbursement
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn">
                <i class="fa-solid fa-print"></i> Print Payslip
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                Close Window
            </button>
        </div>
    </div>

    <!-- Printable Traditional Corporate Payslip -->
    <div class="payslip-card">
        
        <!-- Header -->
        <div class="payslip-header">
            <div class="company-name">{{ $payroll->employee->company_name }}</div>
            <div class="company-sub">Monthly Salary Statement</div>
            <div class="slip-title">Pay Slip for the month of {{ Carbon\Carbon::parse($payroll->month . '-01')->format('F Y') }}</div>
        </div>

        <!-- Employee Info Block -->
        <table class="info-table">
            <tr>
                <td class="info-label">Employee ID</td>
                <td class="info-val">{{ $payroll->employee->employee_id }}</td>
                <td class="info-label">Payment Date</td>
                <td class="info-val">{{ $payroll->processed_at ? $payroll->processed_at->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Employee Name</td>
                <td class="info-val">{{ $payroll->employee->name }}</td>
                <td class="info-label">Bank Name</td>
                <td class="info-val">{{ $payroll->employee->bank_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Designation</td>
                <td class="info-val">{{ $payroll->employee->designation }}</td>
                <td class="info-label">Bank Account No.</td>
                <td class="info-val" style="font-family: monospace;">{{ $payroll->employee->account_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Department</td>
                <td class="info-val">{{ $payroll->employee->department }}</td>
                <td class="info-label">Transaction ID</td>
                <td class="info-val" style="font-family: monospace; font-size: 11px;">{{ $payroll->transaction->transaction_number ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- Earnings and Deductions Table -->
        <table class="salary-table">
            <thead>
                <tr>
                    <th style="width: 50%; border-right: 1px solid #475569;">Earnings</th>
                    <th style="width: 50%;">Deductions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <!-- Earnings List -->
                    <td style="border-right: 1px solid #cbd5e1;">
                        <table class="sub-table">
                            <tr>
                                <td>Basic Salary</td>
                                <td class="amount-col">{{ $currencySymbol }}{{ number_format($payroll->basic_salary, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Performance Bonus</td>
                                <td class="amount-col">{{ $currencySymbol }}{{ number_format($payroll->bonus, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Special Allowance</td>
                                <td class="amount-col">{{ $currencySymbol }}0.00</td>
                            </tr>
                        </table>
                    </td>
                    <!-- Deductions List -->
                    <td>
                        <table class="sub-table">
                            <tr>
                                <td>Income Tax / WHT</td>
                                <td class="amount-col">{{ $currencySymbol }}{{ number_format($payroll->deductions, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Provident Fund / EPF</td>
                                <td class="amount-col">{{ $currencySymbol }}0.00</td>
                            </tr>
                            <tr>
                                <td>Other Adjustments</td>
                                <td class="amount-col">{{ $currencySymbol }}0.00</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- Totals Row -->
                <tr class="total-row">
                    <td style="border-right: 1px solid #cbd5e1;">
                        <table class="sub-table" style="font-weight: 750;">
                            <tr>
                                <td>Total Earnings (Gross)</td>
                                <td class="amount-col">{{ $currencySymbol }}{{ number_format($payroll->basic_salary + $payroll->bonus, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table class="sub-table" style="font-weight: 750;">
                            <tr>
                                <td>Total Deductions</td>
                                <td class="amount-col">{{ $currencySymbol }}{{ number_format($payroll->deductions, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Net Salary Summary Panel -->
        <div class="summary-panel">
            <div class="summary-row">
                <div>
                    <div class="net-pay-title">Net Pay Amount (Transferred)</div>
                    @if($netSalaryInWords)
                        <div class="net-pay-words">Rupees / Dollars: <strong>{{ $netSalaryInWords }} Only</strong></div>
                    @endif
                </div>
                <div class="net-pay-amount">
                    {{ $currencySymbol }}{{ number_format($payroll->net_salary, 2) }}
                </div>
            </div>
        </div>

        <!-- Footer Signatures -->
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Employee Signature</div>
            </div>
            <div class="sig-block" style="display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                <div class="verified-stamp">
                    <i class="fa-solid fa-circle-check"></i> Disbursed
                </div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Authorized Signatory</div>
            </div>
        </div>

    </div>

</body>
</html>
