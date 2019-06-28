<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Resource Review Report</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{!! asset('images/favicon.ico') !!}"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="{{ public_path('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style type="text/css">
     .tr>td{
        margin-bottom: 50px;
     }
     th{
      vertical-align: middle;
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
      <img src="{{ public_path('/imgs/Header_Resource.png') }}" style="width: 100%; height: 50px">
    </header>
    <footer name="page-footer">
      <div class="pagenum-container" style="text-align: center;color:#cac5c5">Page <span class="pagenum"></span></div>
      <img src="{{ public_path('/imgs/Footer_.png') }}" style="width: 100%; height: 50px">
    </footer>
    <main>
      <div class="container-fluid">
        <table class="table">
          <tr>
            <td>
              <label>Name</label>
              <div>{{$user->name}}</div>
            </td>
            <td colspan="2">
              <label>Joining Date</label>
              <div>{{date('d-m-Y', strtotime($user->doj))}}</div>
            </td>
            <td colspan="2">
              <label>Experience</label>
              <div>{{$user->total_experience}}</div>
            </td>
            <td colspan="2">
              <label>Report Date</label>
              <div>{{$user->report_date}}</div>
            </td>
          </tr>
        </table>

        @if(count($user->userTechnologyMapping) > 0)

        <div width="100%">
          <label for="">Technology and Domain : </label>
        </div>

        <table class="table table-bordered" width="100%" page-break-inside: auto;>
          <thead>
            <tr>
              <th class="text-center">Sr. No.</th>
              <th class="text-center">Domain Name</th>
              <th class="text-center">Technology Name</th>
              <th class="text-center">Duration</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($user->userTechnologyMapping as $key2 => $value2)
            <tr>
              <td class="text-center">{{$key2+1}}</td>
              <td>{{$value2->domain->name}}</td>
              <td>{{$value2->technology->name}}</td>
              <td class="text-center">{{$value2->duration == null ? '-' : $value2->duration}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif

        @if(count($user->project_data) > 0)

        <div width="100%">
          <label for="">Project Details : </label>
        </div>

        <table class="table table-bordered" width="100%" page-break-inside: auto;>
          <thead>
          <tr>
              <th class="text-center">Sr. No.</th>
              <th class="text-center">Project Name</th>
              <th class="text-center">Total No. of Tasks</th>
              <th class="text-center">Total Estimated Time (Hrs)</th>
              <th class="text-center">Total Spent Time (Hrs)</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($user->project_data as $key2 => $value2)
            <tr>
              <td class="text-center">{{$key2+1}}</td>
              <td>{{$value2->name}}</td>
              <td class="text-center">{{$value2->total_tasks}}</td>
              <td class="text-center">{{$value2->total_estimation == null ? '-' : $value2->total_estimation}}</td>
              <td class="text-center">{{$value2->total_spent == null ? '-' : $value2->total_spent}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif
      </div>
    </main>
  </body>
</html>
