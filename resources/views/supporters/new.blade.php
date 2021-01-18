@php
    $majors = [
        "mathematics"=>"ریاضی",
        "experimental"=>"تجربی",
        "humanities"=>"انسانی",
        "art"=>"هنر",
        "other"=>"دیگر"
    ];
    $egucation_levels = [
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
                    <a class="btn btn-success" href="{{ route('supporter_student_create') }}">دانش آموز جدید</a>
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
                                <label for="sources_id">منبع</label>
                                <select  id="sources_id" name="sources_id" class="form-control">
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
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="name">نام و نام خانوادگی</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="نام و نام خانوادگی" value="{{ isset($name)?$name:'' }}" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="phone">تلفن</label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="تلفن"  value="{{ isset($phone)?$phone:'' }}" />
                            </div>
                        </div>
                        <div class="col" style="padding-top: 32px;">
                            <a class="btn btn-success" onclick="table.ajax.reload(); return false;" href="#">
                                جستجو
                            </a>
                        </div>
                    </div>
                </form>
                <!--
                <h3 class="text-center">
                    مرتب سازی
                </h3>
                <div class="row">
                  <div class="col text-center p-1">
                    <a class="btn btn-warning btn-block" href="#">سطح بندی</a>
                  </div>
                  <div class="col text-center p-1">
                    <a class="btn btn-warning btn-block" href="#">پیشنهاد فروش</a>
                  </div>
                  <div class="col text-center p-1">
                    <a class="btn btn-warning btn-block" href="#">محصول</a>
                  </div>
                </div>
                <div class="row">
                  <div class="col text-center p-1">
                    <a class="btn btn-warning btn-block" href="#">سایت</a>
                  </div>
                  <div class="col text-center p-1">
                    <a class="btn btn-warning btn-block" href="#">تعداد پیشنهاد فروش</a>
                  </div>
                  <div class="col text-center p-1">
                    <a class="btn btn-warning btn-block" href="#">یادآور</a>
                  </div>
                </div>
                <div class="row">
                    <div class="col text-center p-1">
                      <a class="btn btn-warning btn-block" href="#">برچسب اخلاقی</a>
                    </div>
                    <div class="col text-center p-1">
                      <a class="btn btn-warning btn-block" href="#">برچسب ارزیابی</a>
                    </div>
                    <div class="col text-center p-1">
                    </div>
                </div>
                -->

                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    <th>کد</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>کاربر ثبت کننده</th>
                    <th>منبع ورودی شماره</th>
                    <th>برچسب</th>
                    <th>داغ/سرد</th>
                    <th>توضیحات</th>
                    <th>#</th>
                  </tr>
                  </thead>
                  <tbody>
                    <!--
                      @foreach ($students as $index => $item)
                      <tr id="main-tr-{{ $item->id }}">
                        <td onclick="showMorePanel({{ $index }});">{{ $index + 1 }}</td>
                        <td onclick="showMorePanel({{ $index }});">{{ $item->id }}</td>
                        <td onclick="showMorePanel({{ $index }});">{{ $item->first_name }}</td>
                        <td onclick="showMorePanel({{ $index }});">{{ $item->last_name }}</td>
                        @if($item->user)
                        <td onclick="showMorePanel({{ $index }});">{{ $item->user->first_name . ' ' . $item->user->last_name }}</td>
                        @elseif($item->is_from_site)
                        <td onclick="showMorePanel({{ $index }});">سایت</td>
                        @else
                        <td onclick="showMorePanel({{ $index }});">-</td>
                        @endif
                        <td onclick="showMorePanel({{ $index }});">{{ ($item->source)?$item->source->name:'-' }}</td>
                        @if($item->studenttags && count($item->studenttags)>0)
                        <td>
                            @for($i = 0; $i < count($item->studenttags);$i++)
                            <span class="alert alert-info p-1">
                                {{ $item->studenttags[$i]->tag->name }}
                            </span>
                            @endfor
                        </td>
                        @else
                        <td></td>
                        @endif
                        @if($item->studenttemperatures && count($item->studenttemperatures)>0)
                        <td>
                            @foreach ($item->studenttemperatures as $sitem)
                            @if($sitem->temperature->status=='hot')
                            <span class="alert alert-danger p-1">
                            @else
                            <span class="alert alert-info p-1">
                            @endif
                                {{ $sitem->temperature->name }}
                            </span>
                            @endforeach
                        </td>
                        @else
                        <td></td>
                        @endif
                        <td onclick="showMorePanel({{ $index }});">{{ $item->description }}</td>
                        <td>
                            <a class="btn btn-warning" href="#" onclick="$('#students_index2').val({{ $index }});preloadTemperatureModal();$('#temperature_modal').modal('show'); return false;">
                                داغ/سرد
                            </a>
                            <a class="btn btn-danger" href="#" onclick="seenStudent({{ $item->id }}); return false;">
                                مشاهده شد!
                            </a>
                            <a class="btn btn-primary" href="{{ route('student_edit', ["call_back"=>'supporter_student_new', "id"=>$item->id]) }}">
                                ویرایش
                            </a>
                        </td>
                      </tr>

                      <tr class="morepanel" id="morepanel-{{ $index }}">
                          <td colspan="10">
                              <div class="container">
                                <div class="row">
                                    <div class="col">
                                        تراز یا رتبه سال قبل :
                                        {{ $item->last_year_grade }}
                                    </div>
                                    <div class="col">
                                        مشاور :
                                        {{ ($item->consultant)?$item->consultant->first_name . ' ' . $item->consultant->last_name:'' }}
                                    </div>
                                    <div class="col">
                                        شغل پدر یا مادر :
                                        {{ $item->parents_job_title }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        شماره منزل :
                                        {{ $item->home_phone }}
                                    </div>
                                    <div class="col">
                                        مقطع :
                                        {{ isset($egucation_levels[$item->egucation_level])?$egucation_levels[$item->egucation_level]:$item->egucation_level }}
                                    </div>
                                    <div class="col">
                                        شماره موبایل والدین :
                                        {{ $item->father_phone }}
                                        {{ $item->mother_phone }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        مدرسه :
                                        {{ $item->school }}
                                    </div>
                                    <div class="col">
                                        معدل :
                                        {{ $item->average }}
                                    </div>
                                    <div class="col">
                                        رشته تحصیلی :
                                        {{ isset($majors[$item->major])?$majors[$item->major]:'-' }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">

                                        <a href="{{ route('student_edit', ["call_back"=>'supporter_student_new', "id"=>$item->id]) }}">
                                            ویرایش مشخصات
                                        </a>

                                    </div>
                                    <div class="col">
                                        تاریخ ثبت دانش آموز :
                                        {{ jdate(strtotime($item->created_at))->format("Y/m/d") }}
                                    </div>
                                    <div class="col">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <a href="#" onclick="$('#students_index').val({{ $index }});preloadTagModal('moral');$('#tag_modal').modal('show'); return false;">
                                            برچسب روحیات اخلاقی
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <a href="#" onclick="$('#students_index').val({{ $index }});preloadTagModal('need');$('#tag_modal').modal('show'); return false;">
                                            برچسب نیازهای دانش آموز
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <a target="_blank" href="{{ route('student_purchases', $item->id) }}">
                                            گزارش خریدهای قطعی دانش آموز
                                        </a>
                                    </div>
                                </div>
                              </div>
                          </td>
                      </tr>

                      @endforeach
                    -->
                  </tbody>
                  <!--
                  <tfoot>
                  <tr>
                    <th>Rendering engine</th>
                    <th>Browser</th>
                    <th>Platform(s)</th>
                    <th>Engine version</th>
                    <th>CSS grade</th>
                  </tr>
                  </tfoot>
                  -->
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
                    <br/>
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
                    <br/>
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
                <input type="hidden" id="students_index2" />
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
<script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- page script -->
<script>
    let students = @JSON($students);
    let parentOnes = @JSON($parentOnes);
    let parentTwos = @JSON($parentTwos);
    let parentThrees = @JSON($parentThrees);
    let parentFours = @JSON($parentFours);
    let tmpTags = @JSON($moralTags);
    let tmpCollections = @JSON($needTags);
    let majors = @JSON($majors);
    let egucation_levels = @JSON($egucation_levels);
    let tags = {};
    let collections = {};
    let table;
    for(let tg of tmpTags){
        tags[tg.id] = tg;
    }
    for(let cl of tmpCollections){
        collections[cl.id] = cl;
    }
    let filterParents = {
        parent1: '',
        parent2: '',
        parent3: '',
        parent4: '',
        need_parent1: '',
        need_parent2: '',
        need_parent3: '',
        need_parent4: ''
    }
    function showMorePanel(index, tr){
        console.log(index, tr);
        var persons = {
            student:"دانش آموز",
            father:"پدر",
            mother:"مادر",
            other:"غیره"
        };
        var editRoute = `{{ route('student_edit', ['call_back'=>'supporter_students', 'id'=>-1]) }}`;
        var purchaseRoute = `{{ route('supporter_student_purchases', -1) }}`;
        var supporterStudentAllCallRoute = `{{ route('supporter_student_allcall', -1) }}`;
        var tmpCall = `
            <tr>
                <td>#index#</td>
                <td>#id#</td>
                <td>#product#</td>
                <td>#notice#</td>
                <td>#replier#</td>
                <td>#callresult#</td>
                <td>#next_call#</td>
                <td>#next_to_call#</td>
                <td>#description#</td>
            </tr>`;
        var calls = '';

        var test = `<table style="width: 100%">
            <tr>
                <td>
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                تراز یا رتبه سال قبل :
                                ${ (students[index].last_year_grade!=null)?students[index].last_year_grade:'' }
                            </div>
                            <div class="col">
                                مشاور :
                                ${ (students[index].consultant)?students[index].consultant.first_name + ' ' + students[index].consultant.last_name:'' }
                            </div>
                            <div class="col">
                                شغل پدر یا مادر :
                                ${ (students[index].parents_job_title!=null)?students[index].parents_job_title:'' }
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                شماره منزل :
                                ${ (students[index].home_phone!=null)?students[index].home_phone:'' }
                            </div>
                            <div class="col">
                                مقطع :
                                ${ (students[index].egucation_level!=null)?((egucation_levels[students[index].egucation_level])?egucation_levels[students[index].egucation_level]:students[index].egucation_level):'' }
                            </div>
                            <div class="col">
                                شماره موبایل والدین :
                                ${ (students[index].father_phone!=null)?students[index].father_phone:'' }
                                ${ (students[index].mother_phone!=null)?students[index].mother_phone:'' }
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                مدرسه :
                                ${ (students[index].school)!=null?students[index].school:'' }
                            </div>
                            <div class="col">
                                معدل :
                                ${ (students[index].average!=null)?students[index].average:'' }
                            </div>
                            <div class="col">
                                رشته تحصیلی :
                                ${ (majors[students[index].major])?majors[students[index].major]:'-' }
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="${ editRoute.replace('-1', students[index].id) }">
                                    ویرایش مشخصات
                                </a>
                            </div>
                            <div class="col">
                                تاریخ ثبت دانش آموز :
                                ${ students[index].pcreated_at }
                            </div>
                            <div class="col">
                                تلفن دانش آموز:
                                ${ students[index].phone}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="#" onclick="$('#students_index').val(${ index });preloadTagModal('moral');$('#tag_modal').modal('show'); return false;">
                                    برچسب روحیات اخلاقی
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="#" onclick="$('#students_index').val(${ index });preloadTagModal('need');$('#tag_modal').modal('show'); return false;">
                                    برچسب نیازهای دانش آموز
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a target="_blank" href="${ purchaseRoute.replace('-1', students[index].id) }">
                                    گزارش خریدهای قطعی دانش آموز
                                </a>
                            </div>
                        </div>
                        <!--
                        <div class="row">
                            <div class="col">
                                <a target="_blank" href="${ supporterStudentAllCallRoute.replace('-1', students[index].id) }">
                                    گزارش تماس ها
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a class="btn btn-success" href="#" onclick="students_id = ${ students[index].id };$('#call_modal').modal('show');return false;">
                                    ثبت تماس
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table table-bordered table-hover datatables-all datatables" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ردیف</th>
                                        <th>کد</th>
                                        <th>محصول</th>
                                        <th>اطلاع رسانی</th>
                                        <th>پاسخگو</th>
                                        <th>نتیجه</th>
                                        <th>یادآور</th>
                                        <th>پاسخگو بعد</th>
                                        <th>توضیحات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${ calls }
                                </tbody>
                            </table>
                        </div>
                        -->
                    </div>
                </td>
            </tr>
        </table>`;

        // var tr = $("#tr-" + index)[0];
        var row = table.row(tr);
        if ( row.child.isShown() ) {
            row.child.hide();
        }
        else {
            row.child( test ).show();
        }
    }
    function _showMorePanel(index){
        $('.morepanel').hide();
        $('#morepanel-' + index).show();
    }
    function seenStudent(student_id) {
        $.post('{{ route('supporter_student_seen') }}', {
            student_id
        }, function(result){
            console.log('Result', result);
            if(result.error!=null){
                alert('خطای بروز رسانی');
            }else{
                $("#main-tr-" + result.data.id).remove();
            }
        }).fail(function(){
            alert('خطای بروز رسانی');
        });
    }
    function seeStudent(dobj, student_id) {
        $(dobj).prop('disabled', true).addClass('');
        $.post('{{ route('supporter_student_seen') }}', {student_id}, res => {
            $(dobj).prop('disabled', false);
            console.log(res);
            table.ajax.reload();
        });
    }
    function selectParentOne(dobj){
        filterParents.parent1 = ($(dobj).val()!='')?parseInt($(dobj).val(), 10):'';
        filterTagsByParent()
    }
    function selectParentTwo(dobj){
        filterParents.parent2 = ($(dobj).val()!='')?parseInt($(dobj).val(), 10):'';
        filterTagsByParent()
    }
    function selectParentThree(dobj){
        filterParents.parent3 = ($(dobj).val()!='')?parseInt($(dobj).val(), 10):'';
        filterTagsByParent()
    }
    function selectParentFour(dobj){
        filterParents.parent4 = ($(dobj).val()!='')?parseInt($(dobj).val(), 10):'';
        filterTagsByParent()
    }
    function filterTagsByParent(){
        $("input.tag-checkbox").show();
        $("span.tag-title").show();
        $("input.tag-checkbox").each(function (id, field){
            console.log('checking', field)
            let tagId = parseInt($(field).val(), 10);
            let theTag = tags[tagId];
            console.log(tagId, theTag)
            if(theTag){
                if(filterParents.parent1!=''){
                    if(filterParents.parent1!=theTag.parent1){
                        $(field).hide();
                        $("#tag-title-" + tagId).hide();
                    }
                }
                if(filterParents.parent2!=''){
                    if(filterParents.parent2!=theTag.parent2){
                        $(field).hide();
                        $("#tag-title-" + tagId).hide();
                    }
                }
                if(filterParents.parent3!=''){
                    if(filterParents.parent3!=theTag.parent3){
                        $(field).hide();
                        $("#tag-title-" + tagId).hide();
                    }
                }
                if(filterParents.parent4!=''){
                    if(filterParents.parent4!=theTag.parent4){
                        $(field).hide();
                        $("#tag-title-" + tagId).hide();
                    }
                }
            }

        });
    }
    function selectNeedParentOne(dobj){
        filterParents.need_parent1 = ($(dobj).val()!='')?parseInt($(dobj).val(), 10):'';
        filterNeedTagsByParent()
    }
    function selectNeedParentTwo(dobj){
        filterParents.need_parent2 = ($(dobj).val()!='')?parseInt($(dobj).val(), 10):'';
        filterNeedTagsByParent()
    }
    function selectNeedParentThree(dobj){
        filterParents.need_parent3 = ($(dobj).val()!='')?parseInt($(dobj).val(), 10):'';
        filterNeedTagsByParent()
    }
    function selectNeedParentFour(dobj){
        filterParents.need_parent4 = ($(dobj).val()!='')?parseInt($(dobj).val(), 10):'';
        filterNeedTagsByParent()
    }
    function filterNeedTagsByParent(){
        $("input.needtag-checkbox").show();
        $("span.needtag-title").show();
        $("br.needtag-br").show();
        $("input.needtag-checkbox").each(function (id, field){
            console.log('checking', field)
            let tagId = parseInt($(field).val(), 10);
            let theTag = collections[tagId];
            console.log(tagId, theTag)
            if(theTag){
                if(filterParents.need_parent1!=''){
                    if(filterParents.need_parent1!=theTag.need_parent1){
                        $(field).hide();
                        $("#needtag-title-" + tagId).hide();
                        $("#needtag-br-" + tagId).hide();
                    }
                }
                if(filterParents.need_parent2!=''){
                    if(filterParents.need_parent2!=theTag.need_parent2){
                        $(field).hide();
                        $("#needtag-title-" + tagId).hide();
                        $("#needtag-br-" + tagId).hide();
                    }
                }
                if(filterParents.need_parent3!=''){
                    if(filterParents.need_parent3!=theTag.need_parent3){
                        $(field).hide();
                        $("#needtag-title-" + tagId).hide();
                        $("#needtag-br-" + tagId).hide();
                    }
                }
                if(filterParents.need_parent4!=''){
                    if(filterParents.need_parent4!=theTag.need_parent4){
                        $(field).hide();
                        $("#needtag-title-" + tagId).hide();
                        $("#needtag-br-" + tagId).hide();
                    }
                }
            }

        });
    }
    function selectCollectionOne(dobj){
        $("#collection-two").find('option').show();
        $("#collection-two").find('option[value=""]').prop('selected', true);
        if($(dobj).val()!=''){
            $("#collection-two").find('option').each(function(id, field){
                if($(field).data('parent_id')!=$(dobj).val()){
                    $(field).hide();
                }else{
                    $(field).show();
                }
            });
        }
        $("#collection-three").find('option').show();
        $("#collection-three").find('option[value=""]').prop('selected', true);
        if($(dobj).val()!=''){
            $("#collection-three").find('option').each(function(id, field){
                if($(field).data('parent_parent_id')!=$(dobj).val()){
                    $(field).hide();
                }else{
                    $(field).show();
                }
            });
        }
        filterCollectionsByParent();
    }
    function selectCollectionTwo(dobj){
        console.log('hey');
        $("#collection-three").find('option').show();
        $("#collection-three").find('option[value=""]').prop('selected', true);
        if($(dobj).val()!=''){
            $("#collection-three").find('option').each(function(id, field){
                if($(field).data('parent_id')!=$(dobj).val()){
                    $(field).hide();
                }else{
                    $(field).show();
                }
            });
        }
        filterCollectionsByParent();
    }
    function selectCollectionThree(dobj){
        console.log('hey3');
        filterCollectionsByParent();
    }
    function filterCollectionsByParent(){
        $("input.collection-checkbox").show();
        $("span.collection-title").show();
        let collectionParents = $("#collection-two").val();
        let parents = [];
        if($("#collection-one").val()=='' && collectionParents==''){
            return false;
        }


        parents.push(parseInt($("#collection-one").val(), 10));

        if(collectionParents==''){
            $("#collection-two").find('option').each(function(id, field){
                if($(field).css('display')!='none'){
                    parents.push(parseInt($(field).val(), 10));
                }
            });
        }else {
            parents.push(parseInt(collectionParents, 10))
        }
        console.log(parents);

        $("input.collection-checkbox").each(function (id, field){
            // console.log('checking', field)
            let collectionId = parseInt($(field).val(), 10);
            let theCollection = collections[collectionId];
            console.log(collectionId, theCollection)
            if(theCollection){
                console.log(parents.indexOf(theCollection.id), parents.indexOf(theCollection.parent_id));
                if(parents.indexOf(theCollection.id)<0 && parents.indexOf(theCollection.parent_id)<0){
                    $(field).hide();
                    $("#collection-title-" + collectionId).hide();
                }
            }

        });
    }
    function preloadTagModal(mode){
        if(mode=='need'){
            $("div.needs").show();
            $("div.morals").hide();
        }else{
            $("div.needs").hide();
            $("div.morals").show();
        }
        $("input.tag-checkbox").prop('checked', false);
        $("input.collection-checkbox").prop('checked', false);
        var studentsIndex = parseInt($("#students_index").val(), 10);
        if(!isNaN(studentsIndex)){
            if(students[studentsIndex]){
                console.log(students[studentsIndex].studenttags);
                for(studenttag of students[studentsIndex].studenttags){
                    $("#tag_" + studenttag.tags_id).prop("checked", true);
                    $("#needtag_" + studenttag.tags_id).prop("checked", true);
                }
                console.log(students[studentsIndex].studentcollections);
                for(studentcollection of students[studentsIndex].studentcollections){
                    $("#collection_" + studentcollection.collections_id).prop("checked", true);
                }

            }
        }
    }
    function preloadTemperatureModal(){
        $("input.tag-checkbox").prop('checked', false);
        var studentsIndex = parseInt($("#students_index2").val(), 10);
        if(!isNaN(studentsIndex)){
            if(students[studentsIndex]){
                console.log(students[studentsIndex].studenttemperatures);
                for(studenttag of students[studentsIndex].studenttemperatures){
                    $("#temperature_" + studenttag.temperatures_id).prop("checked", true);
                }
            }
        }
    }
    function saveTags(){
        var selectedTags = [];
        var selectedColllections = [];
        $("input.tag-checkbox:checked").each(function (id , field){
            selectedTags.push(parseInt(field.value, 10));
        });
        $("input.needtag-checkbox:checked").each(function (id , field){
            selectedColllections.push(parseInt(field.value, 10));
        });
        var studentsIndex = parseInt($("#students_index").val(), 10);
        if(!isNaN(studentsIndex)){
            if(students[studentsIndex]){
                console.log('selected tags', selectedTags);
                console.log('selected collections', selectedColllections);
                $.post('{{ route('student_tag') }}', {
                    students_id: students[studentsIndex].id,
                    selectedTags,
                    selectedColllections
                }, function(result){
                    console.log('Result', result);
                    if(result.error!=null){
                        alert('خطای بروز رسانی');
                    }else{
                        window.location.reload();
                    }
                }).fail(function(){
                    alert('خطای بروز رسانی');
                });
            }
        }
    }
    function saveTemperatures(){
        var selectedTemperatures = [];
        $("input.temperature-checkbox:checked").each(function (id , field){
            selectedTemperatures.push(parseInt(field.value, 10));
        });
        var studentsIndex = parseInt($("#students_index2").val(), 10);
        if(!isNaN(studentsIndex)){
            if(students[studentsIndex]){
                console.log('selected temperatures', selectedTemperatures);
                $.post('{{ route('student_temperature') }}', {
                    students_id: students[studentsIndex].id,
                    selectedTemperatures
                }, function(result){
                    console.log('Result', result);
                    if(result.error!=null){
                        alert('خطای بروز رسانی');
                    }else{
                        window.location.reload();
                    }
                }).fail(function(){
                    alert('خطای بروز رسانی');
                });
            }
        }
    }
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".btn-danger").click(function(e){
            if(!confirm('آیا مطمئنید؟')){
                e.preventDefault();
            }
        });
        $('select.select2').select2();

        table = $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "paginate": {
                    "previous": "قبل",
                    "next": "بعد"
                },
                "emptyTable":     "داده ای برای نمایش وجود ندارد",
                "info":           "نمایش _START_ تا _END_ از _TOTAL_ داده",
                "infoEmpty":      "نمایش 0 تا 0 از 0 داده",
            },
            serverSide: true,
            processing: true,
            ajax: {
                "type": "POST",
                "url": "{{ route('supporter_student_new') }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function (data) {
                    data['sources_id'] = $("#sources_id").val();
                    data['name'] = $("#name").val();
                    data['phone'] = $("#phone").val();
                    return JSON.stringify(data);
                },
                "complete": function(response) {
                    console.log(response);
                    $('#example2 tr').click(function() {
                        var tr = this;
                        var studentId = parseInt($(tr).find('td')[1].innerText, 10);
                        if(!isNaN(studentId)){
                            for(var index in students){
                                if(students[index].id==studentId){
                                    showMorePanel(index, tr);
                                }
                            }
                        }
                    });
                }
            }
        });
    });
  </script>
@endsection
