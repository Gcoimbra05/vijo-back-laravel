<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>You have received a video request!</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: #f5f5f5; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { margin-top: 30px; font-size: 12px; color: #888; text-align: center; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .details-table th, .details-table td { text-align: left; padding: 8px; }
        .details-table th { background: #f0f0f0; }
        .details-table tr:nth-child(even) { background: #fafafa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Hello{{ $videoRequest->ref_first_name ? ' ' . $videoRequest->ref_first_name : '' }}!</h2>
        </div>
        <div class="content">
            <p>
                You have received a new video request on the Vijo platform.<br>
                Please see the details below:
            </p>
            <table class="details-table">
                <tr>
                    <th>Catalog ID</th>
                    <td>{{ $videoRequest->catalog_id }}</td>
                </tr>
                @if($videoRequest->ref_first_name)
                <tr>
                    <th>First Name</th>
                    <td>{{ $videoRequest->ref_first_name }}</td>
                </tr>
                @endif
                @if($videoRequest->ref_last_name)
                <tr>
                    <th>Last Name</th>
                    <td>{{ $videoRequest->ref_last_name }}</td>
                </tr>
                @endif
                @if($videoRequest->ref_country_code && $videoRequest->ref_mobile)
                <tr>
                    <th>Phone</th>
                    <td>+{{ $videoRequest->ref_country_code }} {{ $videoRequest->ref_mobile }}</td>
                </tr>
                @endif
                @if($videoRequest->ref_email)
                <tr>
                    <th>Email</th>
                    <td>{{ $videoRequest->ref_email }}</td>
                </tr>
                @endif
                <tr>
                    <th>Status</th>
                    <td>
                        @php
                            $statusMap = [
                                0 => 'Canceled',
                                1 => 'Active',
                                2 => 'Denied',
                                3 => 'Completed'
                            ];
                        @endphp
                        {{ $statusMap[$videoRequest->status] ?? $videoRequest->status }}
                    </td>
                </tr>
                <tr>
                    <th>Request Date</th>
                    <td>{{ $videoRequest->created_at ? $videoRequest->created_at->format('F j, Y H:i') : '-' }}</td>
                </tr>
            </table>
            <p style="margin-top: 20px;">
                For more details, please access the Vijo platform.<br>
                If you have any questions, feel free to contact our support team.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Vijo. All rights reserved.
        </div>
    </div>
</body>
</html>