<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task Notification</title>
  <style>
    /* General Reset */
    body {
      margin: 0;
      padding: 0;
      background: #f4f6f8;
      font-family: Arial, Helvetica, sans-serif;
    }

    /* Mobile Friendly */
    @media only screen and (max-width: 600px) {
      .container { width: 100% !important; padding: 10px !important; }
      .card { display: block !important; width: 100% !important; margin-bottom: 15px !important; }
      .btn { display: block !important; width: 100% !important; text-align: center !important; }
      h1 { font-size: 18px !important; }
      h3 { font-size: 14px !important; }
    }

    /* Reusable Components */
    .section-title {
      text-align: center;
      margin: 0;
      font-size: 16px;
      font-weight: bold;
    }
    .task-box {
      margin-bottom: 10px;
      padding: 10px;
      background: #fff;
      border-radius: 6px;
      font-size: 12px;
      color: #444;
    }
    .task-title {
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 4px;
    }
  </style>
</head>
<body>

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:20px 0;">
    <tr>
      <td align="center">
        <table class="container" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.08);">
          
          <!-- HEADER -->
         <tr>
            <td style="padding:20px;text-align:center;background:linear-gradient(90deg,#0ea5a4,#06b6d4);color:#ffffff;">
                
                <!-- Logo/Image -->
                <img src="{{ asset('images/Saltiii-Logo-White.svg') }}" 
                    alt="{{ config('app.name') }} Logo" 
                    style="max-width:80px;height:auto;margin-bottom:10px;border-radius:8px;">

                <!-- Title -->
                <h1 style="margin:0;font-size:22px;">Task Summary</h1>
                
                <!-- Subtext -->
                <p style="margin:6px 0 0;font-size:13px;">
                {{ config('app.name') }} — {{ $user->name }}
                </p>
            </td>
        </tr>

          <!-- TASK CATEGORIES HORIZONTAL -->
          <tr>
            <td style="padding:20px;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>

                  <!-- DELAYED TASKS -->
                  <td class="card" style="width:33.33%;vertical-align:top;padding-right:10px;">
                    <div style="background:#fff5f5;border:1px solid #fde2e2;border-radius:8px;padding:16px;">
                      <h3 class="section-title" style="color:#b91c1c;">⚠ Delayed <strong>{{ count($tasks['delayed']) }}</strong></h3>
                      @forelse($tasks['delayed'] as $task)
                        <div class="task-box" style="border:1px solid #fcdcdc;">
                          <div class="task-title" style="color:#861518;">{{ $task->title }}</div>
                          <p>
                            {{ optional($task->project)->name ?? 'No Project' }}<br>
                            Due: <strong>{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</strong>
                          </p>
                        </div>
                      @empty
                        <p style="font-size:12px;color:#7f1d1d;text-align:center;">No delayed tasks</p>
                      @endforelse
                    </div>
                  </td>

                  <!-- DUE TODAY TASKS -->
                  <td class="card" style="width:33.33%;vertical-align:top;padding:0 5px;">
                    <div style="background:#fffbf0;border:1px solid #fff1d6;border-radius:8px;padding:16px;">
                      <h3 class="section-title" style="color:#92400e;">📅 Due Today <strong>{{ count($tasks['due_today']) }}</strong></h3>
                      @forelse($tasks['due_today'] as $task)
                        <div class="task-box" style="border:1px solid #fceac7;">
                          <div class="task-title" style="color:#7c2d12;">{{ $task->title }}</div>
                          <p>
                            {{ optional($task->project)->name ?? 'No Project' }}<br>
                            Time: <strong>{{ $task->due_time ?? 'N/A' }}</strong>
                          </p>
                        </div>
                      @empty
                        <p style="font-size:12px;color:#5a3a2a;text-align:center;">No tasks due today</p>
                      @endforelse
                    </div>
                  </td>

                  <!-- UPCOMING TASKS -->
                  <td class="card" style="width:33.33%;vertical-align:top;padding-left:10px;">
                    <div style="background:#f0fdf4;border:1px solid #dcfce7;border-radius:8px;padding:16px;">
                      <h3 class="section-title" style="color:#065f46;">🗂 Upcoming <strong>{{ count($tasks['upcoming']) }}</strong></h3>
                      @forelse($tasks['upcoming'] as $task)
                        <div class="task-box" style="border:1px solid #d4f1e0;">
                          <div class="task-title" style="color:#065f46;">{{ $task->title }}</div>
                          <p>
                            {{ optional($task->project)->name ?? 'No Project' }}<br>
                            Due: <strong>{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</strong>
                          </p>
                        </div>
                      @empty
                        <p style="font-size:12px;color:#065f46;text-align:center;">No upcoming tasks</p>
                      @endforelse
                    </div>
                  </td>

                </tr>
              </table>
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td style="padding:16px;text-align:center;background:#fafafa;border-top:1px solid #eef2f6;font-size:12px;color:#6b7280;">
              <a href="{{ url('/') }}" style="color:#0ea5a4;text-decoration:none;font-weight:bold;">View All Tasks</a><br>
              <span style="display:block;margin-top:8px;">
                {{ config('app.name') }}
              </span>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
