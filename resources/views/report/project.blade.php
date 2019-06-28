<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Project Review Report</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style type="text/css">
      .tr>td{
        margin-bottom: 50px;
      }
      th{
        vertical-align: middle;
      }
      .page-break {
        page-break-after: always;
        /*box-decoration-break: slice;*/
      }
      @page { margin: 100px 25px; }
      header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; }
      footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }
      footer .pagenum:before {
        content: counter(page);
      }
    </style>
  </head>
  <body>
    <header name="page-header">
      <img src="{{ public_path('/imgs/Header_Project.png') }}" style="width: 100%; height: 50px">
    </header>
    <footer name="page-footer">
      <div class="pagenum-container" style="text-align: center;color:#cac5c5">Page <span class="pagenum"></span></div>
      <img src="{{ public_path('/imgs/Footer_.png') }}" style="width: 100%; height: 50px">
    </footer>
    <main>
      <div class="container-fluid">
        @foreach ($projects as $key => $value)
        <table class="table">
          <tr>
            <td>
              <label>Project Name</label>
              <div>{{$value->name}}</div>
            </td>
            <td colspan="2">
              <label>Start Date</label>
              <div>{{date('d-m-Y', strtotime($value->start_date))}}</div>
            </td>
            <td colspan="2">
              <label>End Date</label>
              <div>{{date('d-m-Y', strtotime($value->due_date))}}</div>
            </td>
            <td colspan="2">
              <label>Report Date</label>
              <div>{{$value->report_date}}</div>
            </td>
          </tr>
          <tr>
            <td>
              <label>Lead</label>
              <div>{{$value->lead_name}}</div>
            </td>
            <td colspan="2">
              <label>Manager</label>
              <div>{{$value->user->name}}</div>
            </td>
            <td colspan="4">
              <label>PoC</label>
              <div>
                @if(count($value->projectPoc))
                  @foreach($value->projectPoc as $key1 => $value1)
                  <div>{{$value1->name}}</div>
                  @endforeach
                @endif
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <label>Client</label>
              <div>{{$value->client->name}}</div>
            </td>
            <td colspan="2">
              <label>Status</label>
              <div>{{$value->status_id}}</div>
            </td>
            <td colspan="4">
              <label>No. of Milestones</label>
              <div>{{$value->total_milestones}}</div>
            </td>
          </tr>
        </table>
        <table class="table table-bordered">
          <tr>
            <th style="vertical-align: middle;" rowspan="2">No. of Tasks </th>
            <th class="text-center">Total</th>
            <th class="text-center">Completed</th>
            <th class="text-center">In progress</th>
            <th class="text-center">Pending</th>
            <th class="text-center">Pending for Approval</th>
          </tr>
          <tr>
            <td class="text-center">{{$value->total_tasks}}</td>
            <td class="text-center">{{$value->task_completed}}</td>
            <td class="text-center">{{$value->task_in_progress}}</td>
            <td class="text-center">{{$value->task_pending}}</td>
            <td class="text-center">{{$value->task_pending_app}}</td>
          </tr>
        </table>

        <div width="100%">
          <label for="">Resource Details : </label>
        </div>

        @if(count($value->projectResource) > 0)
        <table class="table table-bordered" width="100%" page-break-inside: auto;>
          <thead>
          <tr>
            <th class="text-center">Sr. No.</th>
            <th class="text-center">Name</th>
            <th class="text-center">Domain</th>
            <th class="text-center">Total Estimated Time (Hrs)</th>
            <th class="text-center">Total Spent Time (Hrs)</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($value->projectResource as $key2 => $value2)
            <tr>
              <td class="text-center">{{$key2+1}}</td>
              <td>{{$value2->user->name}}</td>
              <td>{{$value2->domain->name}}</td>
              <td class="text-center">{{$value2->total_estimation == null ? '-' : $value2->total_estimation}}</td>
              <td class="text-center">{{$value2->total_spent == null ? '-' : $value2->total_spent}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif
        @endforeach
      </div>
    </main>
  </body>
</html>
