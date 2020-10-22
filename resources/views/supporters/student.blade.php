@php
$majors = [
    "mathematics"=>"ریاضی",
    "experimental"=>"تجربی",
    "humanities"=>"انسانی",
    "art"=>"هنر",
    "other"=>"دیگر"
];
$persons = [
    "student"=>"دانش آموز",
    "father"=>"پدر",
    "mother"=>"مادر",
    "other"=>"غیره"
];
$results = [
    "no_answer"=>"بدون پاسخ",
    "unsuccessful"=>"ناموفق",
    "successful"=>"موفق",
    "rejected"=>"در شده"
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
    .morepanel {
        display: none;
    }
    div.dataTables_wrapper {
        width : 100% !important;
    }
</style>
<!-- Date Picker -->
<link href="/plugins/persiancalender/jquery.md.bootstrap.datetimepicker.style.css" rel="stylesheet"/>
@endsection

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>دانش آموزان</h1>
                @if($user)
                <small>{{ $user->first_name }} {{ $user->last_name }}</small>
                @endif
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
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <h3 class="text-center">
                        فیلتر
                    </h3>
                    <form method="post" id="search-frm">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="sources_id">منبع</label>
                                    <select id="sources_id" name="sources_id" class="form-control">
                                        <option value="">همه</option>
                                        @foreach ($sources as $item)
                                        @if(isset($sources_id) && $sources_id==$item->id)
                                        <option value="{{ $item->id }}" selected>
                                            @else
                                        <option value="{{ $item->id }}">
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
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="نام و نام خانوادگی" value="{{ isset($name)?$name:'' }}" />
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="phone">تلفن</label>
                                    <input type="number" class="form-control" id="phone" name="phone" placeholder="تلفن"
                                        value="{{ isset($phone)?$phone:'' }}" />
                                </div>
                            </div>
                            <div class="col" style="padding-top: 32px;">
                                <a class="btn btn-success" onclick="table.ajax.reload(); return false;" href="#">
                                    جستجو
                                </a>
                            </div>
                        </div>
                        <input type="hidden" id="has_collection" name="has_collection" value="{{ isset($has_collection)?$has_collection:'false' }}" />
                        <input type="hidden" id="has_the_product" name="has_the_product" value="{{ isset($has_the_product[0])?implode(',', $has_the_product):'' }}" />
                        <input type="hidden" id="has_the_tags" name="has_the_tags" value="{{ isset($has_the_tags[0])?implode(',', $has_the_tags):'' }}" />
                        <input type="hidden" id="has_call_result" name="has_call_result" value="{{ isset($has_call_result)?$has_call_result:'' }}" />
                        <input type="hidden" id="has_site" name="has_site" value="{{ isset($has_site)?$has_site:'false' }}" />
                        <input type="hidden" id="order_collection" name="order_collection" value="{{ isset($order_collection)?$order_collection:'false' }}" />
                        <input type="hidden" id="has_reminder" name="has_reminder" value="{{ isset($has_reminder)?$has_reminder:'false' }}" />
                        <input type="hidden" id="has_tag" name="has_tag" value="{{ isset($has_tag)?$has_tag:'false' }}" />
                    </form>
                    <h3 class="text-center">
                        مرتب سازی
                    </h3>
                    <div class="row">
                        <div class="col text-center p-1">
                            @if(isset($has_collection) && $has_collection=='true')
                            <a class="btn btn-success btn-block" href="#" onclick="return StudentCollection();">پیشنهاد فروش</a>
                            @else
                            <a class="btn btn-warning btn-block" href="#" onclick="return StudentCollection();">پیشنهاد فروش</a>
                            @endif
                        </div>
                        <div class="col text-center p-1">
                            <!--<a class="btn btn-warning btn-block" href="#">محصول</a>-->
                            <!--
                            <label>
                                محصول:
                            </label>
                            -->
                            <select id="has_product" class="form-control select2" multiple onchange="return selectProduct();">
                                @if(isset($has_the_product[0]))
                                <option value="" disabled>محصول</option>
                                @else
                                <option selected value="" disabled>محصول</option>
                                @endif
                                <option value="">همه</option>
                                @foreach($products as $product)
                                @if(isset($has_the_product[0]) && in_array($product->id,$has_the_product))
                                <option value="{{ $product->id }}">{{($product->parents!='-')?$product->parents . '->':''}} {{ $product->name }}</option>
                                @else
                                <option value="{{ $product->id }}">{{($product->parents!='-')?$product->parents . '->':''}} {{ $product->name }}</option>
                                @endif
                                @endforeach
                            </select>
                            <select id="has_cal_result" class="form-control select2" onchange="return selectCallResult();">
                                @if(isset($has_call_result) && $has_call_result>0)
                                <option value="" disabled>نتیجه تماس</option>
                                @else
                                <option selected value="" disabled>نتیجه تماس</option>
                                @endif
                                <option value="">همه</option>
                                @foreach($callResults as $callResult)
                                @if(isset($has_call_result) && $has_call_result==$callResult->id)
                                <option value="{{ $callResult->id }}" selected>{{ $callResult->title }}</option>
                                @else
                                <option value="{{ $callResult->id }}">{{ $callResult->title }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col text-center p-1">
                            @if(isset($has_site) && $has_site=='true')
                            <a class="btn btn-success btn-block" href="#" onclick="return StudentSite();">سایت</a>
                            @else
                            <a class="btn btn-warning btn-block" href="#" onclick="return StudentSite();">سایت</a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-center p-1">
                            @if(isset($order_collection) && $order_collection=='true')
                            <a class="btn btn-success btn-block" href="#" onclick="return OrderCollection();">تعداد پیشنهاد فروش</a>
                            @else
                            <a class="btn btn-warning btn-block" href="#" onclick="return OrderCollection();">تعداد پیشنهاد فروش</a>
                            @endif
                        </div>
                        <div class="col text-center p-1">
                            @if(isset($has_reminder) && $has_reminder=='true')
                            <a class="btn btn-success btn-block" href="#" onclick="return StudentReminder();">یادآور</a>
                            @else
                            <a class="btn btn-warning btn-block" href="#" onclick="return StudentReminder();">یادآور</a>
                            @endif
                        </div>
                        <div class="col text-center p-1">
                            @if(isset($has_tag) && $has_tag=='true')
                            <!-- <a class="btn btn-success btn-block" href="#" onclick="return StudentTag();">برچسب اخلاقی دارد؟</a> -->
                            @else
                            <!-- <a class="btn btn-warning btn-block" href="#" onclick="return StudentTag();">برچسب اخلاقی دارد؟</a> -->
                            @endif
                            <a class="btn btn-warning btn-block" href="#" onclick="preloadFilterTagModal();$('#tag_modal_filter').modal('show');return false;">
                                برچسب اخلاقی
                            </a>

                            <select id="has_the_tag" class="form-control select22" style="display: none;" multiple> <!--onchange="return selectTag();">-->
                                @if(isset($has_the_tags[0]))
                                <option value="" disabled>برچسب اخلاقی</option>
                                @else
                                <option selected value="" disabled>برچسب اخلاقی</option>
                                @endif
                                <option value="">همه</option>
                                @foreach($moralTags as $tag)
                                @if(isset($has_the_tags[0]) && in_array($tag->id,$has_the_tags))
                                <option value="{{ $tag->id }}" selected>{{ $tag->name }}</option>
                                @else
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!--<div class="row">
                        <div class="col text-center p-1">
                            <a class="btn btn-warning btn-block" href="#">برچسب ارزیابی</a>
                        </div>
                        <div class="col text-center p-1">
                        </div>
                        <div class="col text-center p-1">
                        </div>
                    </div>-->
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
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $index => $item)
                            <!--
                            <tr id="tr-{{ $index }}">
                                <td onclick="showMorePanel({{ $index }});">
                                    {{ $index + 1 }}</td>
                                <td onclick="showMorePanel({{ $index }});">
                                    {{ $item->id }}</td>
                                <td onclick="showMorePanel({{ $index }});">
                                    {{ $item->first_name }}</td>
                                <td onclick="showMorePanel({{ $index }});">
                                    {{ $item->last_name }}</td>
                                <td onclick="showMorePanel({{ $index }});">
                                    {{ ($item->user)?$item->user->first_name . ' ' . $item->user->last_name:'-' }}</td>
                                <td onclick="showMorePanel({{ $index }});">
                                    {{ ($item->source)?$item->source->name:'-' }}</td>
                                @if(($item->studenttags && count($item->studenttags)>0) || ($item->studentcollections && count($item->studentcollections)>0))
                                <td>
                                    @for($i = 0; $i < count($item->studenttags);$i++)
                                    <span class="alert alert-info p-1">
                                        {{ $item->studenttags[$i]->tag->name }}
                                    </span><br/>
                                    @endfor
                                    @for($i = 0; $i < count($item->studentcollections);$i++)
                                    <span class="alert alert-warning p-1">
                                        {{ ($item->studentcollections[$i]->collection->parent) ? $item->studentcollections[$i]->collection->parent->name . '->' : '' }} {{ $item->studentcollections[$i]->collection->name }}
                                    </span><br/>
                                    @endfor
                                </td>
                                @else
                                <td onclick="showMorePanel({{ $index }});"></td>
                                @endif
                                @if($item->studenttemperatures && count($item->studenttemperatures)>0)
                                <td onclick="showMorePanel({{ $index }});">
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
                                <td onclick="showMorePanel({{ $index }});"></td>
                                @endif
                                <td>
                                    <a class="btn btn-warning" href="#"
                                        onclick="$('#students_index').val({{ $index }});preloadTagModal();$('#tag_modal').modal('show'); return false;">
                                        برچسب
                                    </a>
                                    <a class="btn btn-warning" href="#"
                                        onclick="$('#students_index2').val({{ $index }});preloadTemperatureModal();$('#temperature_modal').modal('show'); return false;">
                                        داغ/سرد
                                    </a>
                                    <a class="btn btn-primary" href="{{ route('student_edit', ["call_back"=>'supporter_students', "id"=>$item->id]) }}">
                                        ویرایش
                                    </a>
                                    <a class="btn btn-danger" href="{{ route('student_delete', $item->id) }}">
                                        حذف
                                    </a>
                                </td>
                            </tr>
                             -->
                            @endforeach
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
<!--
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
                    <h3 class="text-center">
                        اخلاقی
                    </h3>
                    @foreach ($moralTags as $index => $item)
                    <input type="checkbox" class="tag-checkbox" id="tag_{{ $item->id }}" value="{{ $item->id }}" />
                    {{ $item->name }}
                    @endforeach
                    <h3 class="text-center">
                        نیازسنجی
                    </h3>
                    @foreach ($needTags as $index => $item)
                    <input type="checkbox" class="collection-checkbox" id="collection_{{ $item->id }}"
                        value="{{ $item->id }}" />
                    {{ $item->name }}
                    @endforeach
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveTags();">اعمال</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
            </div>
        </div>
    </div>
</div>
-->
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
<div class="modal" id="tag_modal_filter" tabindex="-1" role="dialog">
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
                    <h3 class="text-center">
                        اخلاقی
                    </h3>
                    <input type="checkbox" class="filter-tag-checkbox" id="filter-tag_all" value="" onclick="selectFilterAll();" />
                    همه
                    @foreach ($moralTags as $index => $item)
                    <input type="checkbox" class="filter-tag-checkbox" id="filter-tag_{{ $item->id }}" value="{{ $item->id }}" />
                    {{ $item->name }}
                    <br/>
                    @endforeach
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveFilterTags();">اعمال</button>
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
                    <input type="checkbox" class="temperature-checkbox" id="temperature_{{ $item->id }}"
                        value="{{ $item->id }}" />
                    {{ $item->name }}
                    @endforeach
                    <h3 class="text-center">
                        سرد
                    </h3>
                    @foreach ($coldTemperatures as $index => $item)
                    <input type="checkbox" class="temperature-checkbox" id="temperature_{{ $item->id }}"
                        value="{{ $item->id }}" />
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
<div class="modal" id="call_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ثبت تماس</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <div class="form-group">
                        <label for="call_results_id">نتیجه</label>
                        <select class="form-control" id="call_results_id" name="call_results_id">
                            <!--
                            <option value="no_answer">بدون پاسخ</option>
                            <option value="unsuccessful">ناموفق</option>
                            <option value="successful">موفق</option>
                            <option value="rejected">رد شده</option>
                            -->
                            @foreach ($callResults as $item)
                            <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="replier">پاسخگو</label>
                        <select class="form-control" id="replier" name="replier">
                            <option value="student">داشن آموز</option>
                            <option value="father">پدر</option>
                            <option value="mother">مادر</option>
                            <option value="other">غیره</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="products_id">محصول</label>
                        <select class="form-control select2" id="products_id" name="products_id[]" style="width: 100% !important;" multiple>
                            <option value=""></option>
                            @foreach ($products as $item)
                            <option value="{{ $item->id }}">{{($item->parents!='-')?$item->parents . '->':''}} {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="products_id">اطلاع رسانی</label>
                        <select class="form-control select2" id="notices_id" name="notices_id" style="width: 100% !important;">
                            <option value=""></option>
                            @foreach ($notices as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="next_to_call">تماس بعد</label>
                        <select class="form-control" id="next_to_call" name="next_to_call">
                            <option value="student">داشن آموز</option>
                            <option value="father">پدر</option>
                            <option value="mother">مادر</option>
                            <option value="other">غیره</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">زمان تماس بعد</label>
                        <input type="text" class="form-control" id="next_call_persian"placeholder="زمان تماس بعد" readonly />
                        <input type="hidden" id="next_call" name="next_call" />
                    </div>
                    <div class="form-group">
                        <label for="description">توضیحات</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="توضیحات"  />
                    </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveCall();">ثبت</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
            </div>
        </div>
    </div>
</div>
<!-- Date Picker -->
<script src="/plugins/persiancalender/jquery.md.bootstrap.datetimepicker.js"></script>
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
    let egucation_levels = @JSON($egucation_levels);
    let majors = @JSON($majors);
    let tags = {};
    let collections = {};
    var table;
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
        parent4: ''
    }
    let students_id;
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
        var callIndex = 1;
        for(var call of students[index].calls) {
            if(callIndex<=5) {
                calls += tmpCall.replace('#index#', callIndex)
                            .replace('#id#', call.id)
                            .replace('#product#', (call.product)?call.product.name:'-')
                            .replace('#notice#', (call.notice)?call.notice.name:'-')
                            .replace('#replier#', persons[call.replier])
                            .replace('#callresult#', (call.callresult)?call.callresult.title:'-')
                            .replace('#next_call#', (call.next_call)?call.next_call:'-')
                            .replace('#next_to_call#', persons[call.next_to_call])
                            .replace('#description#', (call.next_call)?call.next_call:'-');
            }else {
                continue;
            }
            callIndex++;
        }
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
    function changeSupporter(studentsIndex) {
        if (students[studentsIndex]) {
            var students_id = students[studentsIndex].id;
            var supporters_id = $("#supporters_id_" + studentsIndex).val();
            $("#loading-" + studentsIndex).show();
            $.post('{{ route('student_supporter') }}', {
                    students_id,
                    supporters_id
                },
                function (result) {
                    $("#loading-" + studentsIndex).hide();
                    console.log('Result', result);
                    if (result.error != null) {
                        alert('خطای بروز رسانی');
                    }
                }).fail(function () {
                $("#loading-" + studentsIndex).hide();
                alert('خطای بروز رسانی');
            });
        }
        return false;
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
        $("br.tag-br").show();
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
                        $("#tag-br-" + tagId).hide();
                    }
                }
                if(filterParents.parent2!=''){
                    if(filterParents.parent2!=theTag.parent2){
                        $(field).hide();
                        $("#tag-title-" + tagId).hide();
                        $("#tag-br-" + tagId).hide();
                    }
                }
                if(filterParents.parent3!=''){
                    if(filterParents.parent3!=theTag.parent3){
                        $(field).hide();
                        $("#tag-title-" + tagId).hide();
                        $("#tag-br-" + tagId).hide();
                    }
                }
                if(filterParents.parent4!=''){
                    if(filterParents.parent4!=theTag.parent4){
                        $(field).hide();
                        $("#tag-title-" + tagId).hide();
                        $("#tag-br-" + tagId).hide();
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
                if($(field).data('parent_id')!=$(dobj).val() && $(field).val()!=''){
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
        $("br.collection-br").show();
        let collectionParents = $("#collection-two").val();
        let parents = [];
        if($("#collection-one").val()=='' && collectionParents==''){
            return false;
        }




        if(collectionParents==''){
            parents.push(parseInt($("#collection-one").val(), 10));
            $("#collection-two").find('option').each(function(id, field){
                if($(field).css('display')!='none'){
                    parents.push(parseInt($(field).val(), 10));
                }
            });
            $("input.collection-checkbox").each(function (id, field){
                let collectionId = parseInt($(field).val(), 10);
                if(parents.indexOf(collectionId)<0){
                    $(field).hide();
                    $("#collection-title-" + collectionId).hide();
                    $("#collection-br-" + collectionId).hide();
                }
            });
            return false;
        }else {
            parents.push(parseInt(collectionParents, 10))
        }
        console.log('parents:', parents);

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
                    $("#collection-br-" + collectionId).hide();
                }
            }

        });
    }
    function preloadFilterTagModal() {
        $("input.filter-tag-checkbox").prop('checked', false);
        var selecteds = $("#has_the_tag").val();
        console.log(selecteds);
        for(var i of selecteds) {
            if(i=='') {
                $("input.filter-tag-checkbox").prop('checked', true);
            }
            $("#filter-tag_" + i).prop('checked', true);
        }
    }

    function saveFilterTags() {
        console.log('SAVE')
        $(`#has_the_tag option`).prop('selected', false);
        $("input.filter-tag-checkbox:checked").each(function (id, field) {
            console.log(`#has_the_tag option[value='${$(field).val()}']`);
            $(`#has_the_tag option[value='${$(field).val()}']`).prop('selected', true);
        });
        $('#tag_modal_filter').modal('hide');
        console.log($("#has_the_tag").val());
        $("#has_the_tags").val($("#has_the_tag").val().join(','));
    }

    function selectFilterAll() {
        var state = $("#filter-tag_all").prop('checked');
        console.log('State', state);
        $("input.filter-tag-checkbox").each(function(id, field){
            console.log(field);
            if(field.id!="filter-tag_all") {
                console.log('set', field.id, state);
                $(field).prop('checked', state);
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
                }
                console.log(students[studentsIndex].studentcollections);
                for(studentcollection of students[studentsIndex].studentcollections){
                    $("#collection_" + studentcollection.collections_id).prop("checked", true);
                }

            }
        }
    }

    function preloadTemperatureModal() {
        $("input.tag-checkbox").prop('checked', false);
        var studentsIndex = parseInt($("#students_index2").val(), 10);
        if (!isNaN(studentsIndex)) {
            if (students[studentsIndex]) {
                console.log(students[studentsIndex].studenttemperatures);
                for (studenttag of students[studentsIndex].studenttemperatures) {
                    $("#temperature_" + studenttag.temperatures_id).prop("checked", true);
                }
            }
        }
    }

    function saveTags() {
        var selectedTags = [];
        var selectedColllections = [];
        $("input.tag-checkbox:checked").each(function (id, field) {
            selectedTags.push(parseInt(field.value, 10));
        });
        $("input.collection-checkbox:checked").each(function (id, field) {
            selectedColllections.push(parseInt(field.value, 10));
        });
        var studentsIndex = parseInt($("#students_index").val(), 10);
        if (!isNaN(studentsIndex)) {
            if (students[studentsIndex]) {
                console.log('selected tags', selectedTags);
                console.log('selected collections', selectedColllections);
                $.post('{{ route('student_tag') }}', {
                        students_id: students[studentsIndex].id,
                        selectedTags,
                        selectedColllections
                    },
                    function (result) {
                        console.log('Result', result);
                        if (result.error != null) {
                            alert('خطای بروز رسانی');
                        } else {
                            window.location.reload();
                        }
                    }).fail(function () {
                    alert('خطای بروز رسانی');
                });
            }
        }
    }

    function saveTemperatures() {
        var selectedTemperatures = [];
        $("input.temperature-checkbox:checked").each(function (id, field) {
            selectedTemperatures.push(parseInt(field.value, 10));
        });
        var studentsIndex = parseInt($("#students_index2").val(), 10);
        if (!isNaN(studentsIndex)) {
            if (students[studentsIndex]) {
                console.log('selected temperatures', selectedTemperatures);
                $.post('{{ route('student_temperature') }}', {
                        students_id: students[studentsIndex].id,
                        selectedTemperatures
                    },
                    function (result) {
                        console.log('Result', result);
                        if (result.error != null) {
                            alert('خطای بروز رسانی');
                        } else {
                            window.location.reload();
                        }
                    }).fail(function () {
                    alert('خطای بروز رسانی');
                });
            }
        }
    }

    function saveCall() {
        $.post('{{ route('supporter_student_call') }}', {
                students_id,
                description: $("#description").val(),
                result: $("#result").val(),
                replier: $("#replier").val(),
                products_id: $("#products_id").val(),
                notices_id: $("#notices_id").val(),
                next_to_call: $("#next_to_call").val(),
                next_call: $("#next_call").val()
        },
        function (result) {
                console.log('Result', result);
                if (result.error != null) {
                    alert('خطای بروز رسانی');
                } else {
                    window.location.reload();
                }
        }).fail(function () {
            alert('خطای بروز رسانی');
        });
    }

    function StudentCollection(){
        var has_collection = $("#has_collection").val().trim();
        if(has_collection=='' || has_collection=='false'){
            has_collection = 'true';
        }else {
            has_collection = 'false';
        }
        $("#has_collection").val(has_collection);
        // $("#search-frm").submit();
        table.ajax.reload();
        return false;
    }

    function StudentTag(){
        var has_tag = $("#has_tag").val().trim();
        if(has_tag=='' || has_tag=='false'){
            has_tag = 'true';
        }else {
            has_tag = 'false';
        }
        $("#has_tag").val(has_tag);
        // $("#search-frm").submit();
        table.ajax.reload();
        return false;
    }

    function StudentReminder(){
        var has_reminder = $("#has_reminder").val().trim();
        if(has_reminder=='' || has_reminder=='false'){
            has_reminder = 'true';
        }else {
            has_reminder = 'false';
        }
        $("#has_reminder").val(has_reminder);
        // $("#search-frm").submit();
        table.ajax.reload();
        return false;
    }

    function StudentSite() {
        var has_site = $("#has_site").val().trim();
        if(has_site=='' || has_site=='false'){
            has_site = 'true';
        }else {
            has_site = 'false';
        }
        $("#has_site").val(has_site);
        // $("#search-frm").submit();
        table.ajax.reload();
        return false;
    }

    function OrderCollection() {
        var order_collection = $("#order_collection").val().trim();
        if(order_collection=='' || order_collection=='false'){
            order_collection = 'true';
        }else {
            order_collection = 'false';
        }
        $("#order_collection").val(order_collection);
        // $("#search-frm").submit();
        table.ajax.reload();
        return false;
    }

    function selectProduct(){
        $("#has_the_product").val($("#has_product").val().join(','));
        // $("#search-frm").submit();
    }

    function selectTag(){
        $("#has_the_tags").val($("#has_the_tag").val().join(','));
    }

    function selectCallResult(){
        $("#has_call_result").val($("#has_cal_result").val());
        // $("#search-frm").submit();
        table.ajax.reload();
    }

    $(function () {
        $('#next_call_persian').MdPersianDateTimePicker({
            targetTextSelector: '#next_call_persian',
            targetDateSelector: '#next_call',
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".btn-danger").click(function (e) {
            if (!confirm('آیا مطمئنید؟')) {
                e.preventDefault();
            }
        });
        $('select.select2').select2();

        // $("table.datatables").DataTable({
        //     "paging": true,
        //     "lengthChange": false,
        //     "searching": false,
        //     "ordering": true,
        //     "info": true,
        //     "autoWidth": true,
        //     "language": {
        //         "paginate": {
        //             "previous": "قبل",
        //             "next": "بعد"
        //         },
        //         "emptyTable": "داده ای برای نمایش وجود ندارد",
        //         "info": "نمایش _START_ تا _END_ از _TOTAL_ داده",
        //         "infoEmpty": "نمایش 0 تا 0 از 0 داده",
        //     }
        // });
        table = $("#example2").DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "language": {
                "paginate": {
                    "previous": "قبل",
                    "next": "بعد"
                },
                "emptyTable": "داده ای برای نمایش وجود ندارد",
                "info": "نمایش _START_ تا _END_ از _TOTAL_ داده",
                "infoEmpty": "نمایش 0 تا 0 از 0 داده",
            },
            serverSide: true,
            processing: true,
            ajax: {
                "type": "POST",
                "url": "{{ route('supporter_students') }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function (data) {
                    data['sources_id'] = $("#sources_id").val();
                    data['name'] = $("#name").val();
                    data['phone'] = $("#phone").val();
                    data['has_collection'] = $("#has_collection").val();
                    data['has_the_product'] = $("#has_the_product").val();
                    data['has_the_tags'] = $("#has_the_tags").val();
                    data['has_call_result'] = $("#has_call_result").val();
                    data['has_site'] = $("#has_site").val();
                    data['order_collection'] = $("#order_collection").val();
                    data['has_reminder'] = $("#has_reminder").val();
                    data['has_tag'] = $("#has_tag").val();
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
