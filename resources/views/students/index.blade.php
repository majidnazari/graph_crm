@php
    $majors = [
        "mathematics"=>"ریاضی",
        "experimental"=>"تجربی",
        "humanities"=>"انسانی",
        "art"=>"هنر",
        "other"=>"دیگر"
    ];
    $egucation_levels = [
        "6" => "6",
        "7" => "7",
        "8" => "8",
        "9" => "9",
        "10" => "10",
        "11" => "11",
        "12" => "12",
        "13" => "فارغ التحصیل",
        "14" => "دانشجو",
        null => ""
    ];
@endphp
@extends('layouts.index')

@section('css')
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .morepanel{
        display: none;
    }
</style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>دانش آموزان</h1>
            </div>
            <div class="col-sm-6">
              <!--
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">DataTables</li>
              </ol>
              -->
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                    <a class="btn btn-success" href="{{ route('student_create') }}">دانش آموز جدید</a>
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">

                <h3 class="text-center">
                   فیلتر
                </h3>
                <form method="post">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="supporters_id">پشتیبان</label>
                                <select  id="supporters_id" name="supporters_id" class="form-control select2" onchange="theChange()">
                                    <option value="">همه</option>
                                    @foreach ($supports as $item)
                                        @if(isset($supporters_id) && $supporters_id==$item->id)
                                        <option value="{{ $item->id }}" selected >
                                        @else
                                        <option value="{{ $item->id }}" >
                                        @endif
                                        {{ $item->first_name }} {{ $item->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- <div class="col">
                            <div class="form-group">
                                <label for="sources_id">منبع</label>
                                <select  id="sources_id" name="sources_id" class="form-control" onchange="theChange()">
                                    <option value="">همه</option>
                                    @foreach ($sources as $item)
                                        @if(isset($sources_id) && $sources_id==$item->id)
                                        <option value="{{ $item->id }}" selected >
                                        @else
                                        <option value="{{ $item->id }}" >
                                        @endif
                                        {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> -->
                        @if($route == "student_all")
                        <div class="col">
                            <div class="form-group">
                                <label for="level">سطح</label>
                                <select  id="level" name="level" class="form-control" onchange="theChange()">
                                    <option value="">همه</option>
                                    @for($i = 1; $i <= 4; $i++)
                                       @if(isset($level) && $level ==$i)
                                       <option value="{{ $i }}" selected >
                                       @else
                                       <option value="{{ $i }}" >
                                       @endif
                                       {{ $i }}
                                       </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col">
                            <div class="form-group">
                                <label for="name">نام و نام خانوادگی</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="نام و نام خانوادگی" value="{{ isset($name)?$name:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="phone">تلفن</label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="تلفن"  value="{{ isset($phone)?$phone:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="cities_id">شهر</label>
                                <select  id="cities_id" name="cities_id" class="form-control select2" onchange="theChange()">
                                    <option value="">همه</option>
                                    @foreach ($cities as $item)
                                        @if(isset($cities_id) && $cities_id==$item->id)
                                        <option value="{{ $item->id }}" selected >
                                        @else
                                        <option value="{{ $item->id }}" >
                                        @endif
                                        {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="egucation_level">مقطع</label>
                                <select  id="egucation_level" name="egucation_level" class="form-control" onchange="theChange()">
                                    <option value="">همه</option>
                                    @foreach ($egucation_levels as $key => $item)
                                        @if($key!=null)
                                        @if(isset($egucation_level) && $egucation_level==$key)
                                        <option value="{{ $key }}" selected >
                                        @else
                                        <option value="{{ $key }}" >
                                        @endif
                                        {{ $item }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="concours_year">سال کنکور</label>
                                <input type="number" class="form-control" id="concours_year" name="concours_year" placeholder="سال کنکور"  value="{{ isset($concours_year)?$concours_year:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="major">رشته</label>
                                <select  id="major" name="major" class="form-control" onchange="theChange()">
                                    <option value="">همه</option>
                                    @foreach ($majors as $key => $item)
                                        @if(isset($egucation_level) && $egucation_level==$key)
                                        <option value="{{ $key }}" selected >
                                        @else
                                        <option value="{{ $key }}" >
                                        @endif
                                        {{ $item }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="school">مدرسه</label>
                                <input type="text" class="form-control" id="school" name="school" placeholder="مدرسه"  value="{{ isset($school)?$school:'' }}" onkeypress="handle(event)"/>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="nationality_code">کد ملی</label>
                                <input type="number" class="form-control" id="nationality_code" name="nationality_code" placeholder="کد ملی"  value="{{ isset($nationality_code)?$nationality_code:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                        <div class="col" style="padding-top: 32px;">
                            <a class="btn btn-success" onclick="theSearch()" href="#">
                                جستجو
                            </a>
                            <img id="loading" src="/dist/img/loading.gif" style="height: 20px;display: none;" />
                        </div>
                    </div>
                </form>

                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr class="table_header">
                    <th>ردیف</th>
                    <th>کد</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>کاربر ثبت کننده</th>
                    <th>کاربر ویرایش کننده</th>
                    <th>منبع ورودی شماره</th>
                    <th>برچسب</th>
                    <th>داغ/سرد</th>
                    <th>پشتیبان</th>
                    @if($route == "student_all")
                    <th>سطح دانش آموز</th>
                    @endif
                    <th>توضیحات</th>
                    <th>#</th>
                  </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </section>
      <!-- /.content -->
@endsection

@section('js')
<div class="modal" id="tag_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">برچسب</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <p>
                <input type="hidden" id="students_index" />
                <div class="morals">
                <h3 class="text-center">
                    اخلاقی
                </h3>
                <div>
                    <select id="parent-one" onchange="selectParentOne(this);">
                        <option value="">همه</option>
                        @foreach ($parentOnes as $item)
                        <option value="{{ $item->id }}">{{$item->name}}</option>
                        @endforeach
                    </select>

                    <select id="parent-two" onchange="selectParentTwo(this);">
                        <option value="">همه</option>
                        @foreach ($parentTwos as $item)
                        <option value="{{ $item->id }}">{{$item->name}}</option>
                        @endforeach
                    </select>

                    <select id="parent-three" onchange="selectParentThree(this);">
                        <option value="">همه</option>
                        @foreach ($parentThrees as $item)
                        <option value="{{ $item->id }}">{{$item->name}}</option>
                        @endforeach
                    </select>

                    <select id="parent-four" onchange="selectParentFour(this);">
                        <option value="">همه</option>
                        @foreach ($parentFours as $item)
                        <option value="{{ $item->id }}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                @foreach ($moralTags as $index => $item)
                    <input type="checkbox" class="tag-checkbox" id="tag_{{ $item->id }}" value="{{ $item->id }}" />
                    <span class="tag-title" id="tag-title-{{ $item->id }}">
                    {{ $item->name }}
                    </span>
                    <br class="tag-br" id="tag-br-{{ $item->id }}"/>
                @endforeach
                </div>
                <div class="needs">
                <h3 class="text-center">
                    نیازسنجی
                </h3>
                <div>
                    <select id="need-parent-one" onchange="selectNeedParentOne(this);">
                        <option value="">همه</option>
                        @foreach ($needTagParentOnes as $item)
                        <option value="{{ $item->id }}">{{$item->name}}</option>
                        @endforeach
                    </select>

                    <select id="need-parent-two" onchange="selectNeedParentTwo(this);">
                        <option value="">همه</option>
                        @foreach ($needTagParentTwos as $item)
                        <option value="{{ $item->id }}">{{$item->name}}</option>
                        @endforeach
                    </select>

                    <select id="need-parent-three" onchange="selectNeedParentThree(this);">
                        <option value="">همه</option>
                        @foreach ($needTagParentThrees as $item)
                        <option value="{{ $item->id }}">{{$item->name}}</option>
                        @endforeach
                    </select>

                    <select id="need-parent-four" onchange="selectNeedParentFour(this);">
                        <option value="">همه</option>
                        @foreach ($needTagParentFours as $item)
                        <option value="{{ $item->id }}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                @foreach ($needTags as $index => $item)
                    <input type="checkbox" class="needtag-checkbox" id="needtag_{{ $item->id }}" value="{{ $item->id }}" />
                    <span class="needtag-title" id="needtag-title-{{ $item->id }}">
                    {{ $item->name }}
                    </span>
                    <br class="needtag-br" id="needtag-br-{{ $item->id }}"/>
                @endforeach
                <!--
                <div>
                    <select id="collection-one" onchange="selectCollectionOne(this);">
                        <option value="">همه</option>
                        @foreach ($firstCollections as $item)
                        <option value="{{ $item->id }}">{{$item->name}}</option>
                        @endforeach
                    </select>

                    <select id="collection-two" onchange="selectCollectionTwo(this);">
                        <option value="">همه</option>
                        @foreach ($secondCollections as $item)
                        <option value="{{ $item->id }}" data-parent_id="{{$item->parent_id}}">{{$item->name}}</option>
                        @endforeach
                    </select>

                    <select id="collection-three" onchange="selectCollectionThree(this);">
                        <option value="">همه</option>
                        @foreach ($thirdCollections as $item)
                        <option value="{{ $item->id }}" data-parent_id="{{$item->parent_id}}" data-parent_parent_id="{{$item->parent->parent_id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                @foreach ($needTags as $index => $item)
                    <input type="checkbox" class="collection-checkbox" id="collection_{{ $item->id }}" value="{{ $item->id }}" />
                    <span class="collection-title" id="collection-title-{{ $item->id }}">
                    {{ $item->name }}
                    </span>
                    <br class="collection-br" id="collection-br-{{ $item->id }}"/>
                @endforeach
                -->
                </div>
            </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="saveTags();">اعمال</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
        </div>
      </div>
    </div>
</div>
<div class="modal" id="temperature_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content"> 
        <div class="modal-header">
          <h5 class="modal-title">داغ/سرد</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <p>
                <h3 class="text-center">
                    داغ
                </h3>
                @foreach ($hotTemperatures as $index => $item)
                    <input type="checkbox" class="temperature-checkbox" id="temperature_{{ $item->id }}" value="{{ $item->id }}" />
                    {{ $item->name }}
                @endforeach
                <h3 class="text-center">
                    سرد
                </h3>
                @foreach ($coldTemperatures as $index => $item)
                    <input type="checkbox" class="temperature-checkbox" id="temperature_{{ $item->id }}" value="{{ $item->id }}" />
                    {{ $item->name }}
                @endforeach
            </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="saveTemperatures();">اعمال</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
        </div>
      </div>
    </div>
</div>
<!-- Select2 -->
<script src="/plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables -->
<!-- <script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script> -->

<script src="../../plugins/DataTables/DataTables-1.13.4/js/jquery.dataTables.js"></script>  
<script src="../../plugins/DataTables/DataTables-1.13.4/js/dataTables.bootstrap4.js"></script>  

<!-- page script -->
<script type="text/javascript">
    let students = @JSON($Students);
    let parentOnes = @JSON($parentOnes);
    let parentTwos = @JSON($parentTwos);
    let parentThrees = @JSON($parentThrees);
    let parentFours = @JSON($parentFours);
    let tmpTags = @JSON($moralTags);
    let tmpCollections = @JSON($needTags);
    let egucation_levels = @JSON($egucation_levels);
    let majors = @JSON($majors);
    let theRoute = "{{ $route }}";
    let route = "{{ route('student_all') }}";
    let route_student_supporter = "{{ route('student_supporter') }}";
    let route_student_tag = "{{ route('student_tag') }}";
    let route_student_temperature = "{{ route('student_temperature') }}";
    let route_edit = "{{ route('student_edit', ['call_back'=>'student_all', 'id'=>-1]) }}";
    let route_purchase = "{{ route('student_purchases', -1) }}";
    let route_call=`{{ route('supporter_student_allcall', -1) }}`;
    let levelRoute = "{{ route('change_level_ajax')}}";
</script>
<script type="text/javascript" src="/dist/js/allStudents.js"></script>
@endsection
