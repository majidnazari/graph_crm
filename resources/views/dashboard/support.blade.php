@extends('layouts.index')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-lg-10 col-md-10 col-sm-9 col-xs-6">
              <h1 class="m-0 text-dark">داشبورد</h1>
            </div><!-- /.col -->
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
               <div class="small-box bg-success">
                   <div class="inner p-0">
                     <p class="text-center">
                       <a href="{{route('supporter_sanads', Auth::user()->id)}}"> <span class="text-light btn" >
                            فروش {{ jdate()->format('F') }} ماه من
                        <span class="badge badge-light right">{{ $sum }} تومان</span>
                        </a>
                        </span>
                     </p>
                   </div>
               </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-4 col-6">
              <!-- small box -->
              <div class="small-box bg-orange">
                <div class="inner p-0">
                  <!--<h3>150</h3>-->

                    <p class="text-center">
                        <a href="{{ route('supporter_student_new') }}" class="text-light btn">
                        ورودی جدید
                        <span class="badge badge-warning right">{{ $newStudents }}</span>
                        </a>
                    </p>
                </div>
                <!--
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                -->
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-orange">
                  <div class="inner p-0">
                    <!--<h3>150</h3>-->

                      <p class="text-center">
                        <a href="{{ route('messages') }}" class="text-light btn">
                            پیام های دریافتی
                            <span class="badge badge-warning right">{{ count($usermessages) }}</span>
                        </a>
                    </p>
                  </div>
                  <!--
                  <div class="icon">
                    <i class="ion ion-bag"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  -->
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-orange">
                  <div class="inner p-0">
                    <!--<h3>150</h3>-->

                      <p class="text-center">
                        <a href="#" class="text-light btn">
                            یادآورهای روز قبل
                            <span class="badge badge-warning right">{{ count($yesterday_recalls) }}</span>
                        </a>
                    </p>
                  </div>
                  <!--
                  <div class="icon">
                    <i class="ion ion-bag"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  -->
                </div>
            </div>
            <!-- ./col -->
          </div>
          <!-- /.row -->
          <div class="row">
              <div class="col-4">
                <div class="small-box bg-orange">
                    <div class="inner p-0">
                      <!--<h3>150</h3>-->

                        <p class="text-center">
                            <a href="{{ $count_of_yesterday_missed_calls ? route('supporter_yesterday_missed_calls') :'#' }}" class="text-light btn">
                            تعداد تماس های بی پاسخ روز قبل
                            <span class="badge badge-warning right">{{ $count_of_yesterday_missed_calls }}</span>
                            </a>
                        </p>
                    </div>
                    <!--
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    -->
                  </div>
              </div>
              <div class="col-4">
                <div class="small-box bg-orange">
                    <div class="inner p-0">
                      <!--<h3>150</h3>-->

                        <p class="text-center">
                            <a href="{{ $count_of_all_missed_calls ? route('supporter_all_missed_calls') : '#' }}" class="text-light btn">
                            تعداد کل تماس های بی پاسخ
                            <span class="badge badge-warning right">{{ $count_of_all_missed_calls }}</span>
                            </a>
                        </p>
                    </div>
                    <!--
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    -->
                  </div>
              </div>
              <div class="col-4">
                <div class="small-box bg-orange">
                    <div class="inner p-0">
                      <!--<h3>150</h3>-->

                        <p class="text-center">
                            <a href="{{ $count_no_need_calls_students ? route('no_need_students') : '#' }}" class="text-light btn">
                             تعداد کاربران با تماس عدم نیاز
                            <span class="badge badge-warning right">{{ $count_no_need_calls_students }}</span>
                            </a>
                        </p>
                    </div>
                    <!--
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    -->
                  </div>
              </div>
          </div>
          <div class="row">
            <div class="col-md-3">
                <h4 style="margin-top: 110px;">
                    تعداد کل ورودی های جدید
                </h4>
            </div>
            <div class="col-md-9">
                <div class="chart">
                    <div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                    <canvas id="areaChart" style="height: 250px; min-height: 250px; display: block; width: 524px;" width="524" height="250" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
          </div>
          <div class="row">
            <h4 style="margin-top: 70px;">
                تعداد یادآورها
            </h4>
            <table class="table">
                <thead>
                  <tr>
                    @for($i = 0; $i < 10; $i++)
                    <th scope="col">{{ jdate()->addDays($i)->format('%d %B')}}</th>
                    @endfor
                  </tr>
                </thead>
                <tbody>
                  <tr>
                      <td><a href="{{ $todayCount ? route('reminders_get',['date' => 'today']) : '#' }}">{{ $todayCount }}</a></td>
                    @foreach($arrOfReminders as $item)
                      <td><a href="{{ $item ? route('reminders_get',['date' => 'all']) : '#'}}">{{ $item }}</a></td>
                    @endforeach
                  </tr>
                </tbody>
              </table>
          </div>
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
@endsection

@section('js')
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/dist/js/demo.js"></script>
<script>
    $(function () {
      /* ChartJS
       * -------
       * Here we will create a few charts using ChartJS
       */

      //--------------
      //- AREA CHART -
      //--------------

      // Get context with jQuery - using jQuery's .get() method.
      var areaChartCanvas = $('#areaChart').get(0).getContext('2d')
      var mainLabels = [
             'فروردین'
            , 'اردیبهشت'
            , 'خرداد'
            , 'تیر'
            , 'مرداد'
            , 'شهریور'
            , 'مهر'
            , 'آبان'
            , 'آذر'
            , 'دی'
            , 'بهمن'
            , 'اسفند'
        ];
      var labels = [];
      var results = @JSON($results);
      for(var i = 0;i < results.length;i++){
          labels.push(mainLabels[i])
      }
      console.log(mainLabels, labels)
      var areaChartData = {
        labels ,
        datasets: [
          {
            label               : 'دانش آموزان',
            backgroundColor     : 'rgba(60,141,188,0.9)',
            borderColor         : 'rgba(60,141,188,0.8)',
            pointRadius          : false,
            pointColor          : '#3b8bba',
            pointStrokeColor    : 'rgba(60,141,188,1)',
            pointHighlightFill  : '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data                : results
          },
        ]
      }

      var areaChartOptions = {
        maintainAspectRatio : false,
        responsive : true,
        legend: {
          display: false
        },
        scales: {
          xAxes: [{
            gridLines : {
              display : false,
            }
          }],
          yAxes: [{
            gridLines : {
              display : false,
            }
          }]
        }
      }

      // This will get the first returned node in the jQuery collection.
      var areaChart       = new Chart(areaChartCanvas, {
        type: 'line',
        data: areaChartData,
        options: areaChartOptions
      })
    })
  </script>
@endsection
