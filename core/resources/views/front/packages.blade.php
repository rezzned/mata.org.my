@extends("front.$version.layout")

@section('pagename')
- {{__('Membership')}}
@endsection

@section('meta-keywords', "$be->packages_meta_keywords")
@section('meta-description', "$be->packages_meta_description")


@section('breadcrumb-title', $be->pricing_title)
@section('breadcrumb-subtitle', $be->pricing_subtitle)
@section('breadcrumb-link', __('Membership'))


@section('content')
<!--    Packages section start   -->
<div class="pricing-tables pricing-page" id="masonry-package">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        @if (count($categories) > 0 && $bex->package_category_status == 1)
          <div class="filter-nav text-center mb-15">
            <ul class="filter-btn">
              <li data-filter="*" class="active">{{__('All')}}</li>
              @foreach ($categories as $category)
                @php
                    $filterValue = "." . Str::slug($category->name);
                @endphp

                <li data-filter="{{ $filterValue }}">{{ $category->name }}</li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>
    </div>

    {{-- <div class="masonry-row clearfix"> --}}
    <div class="">
      <div class="row">
        @if (count($packages) == 0)
          <div class="col">
            <h3 class="text-center">{{ __('No Package Found!') }}</h3>
          </div>
        @else
          @foreach ($packages as $key => $package)
            @php
              $packageCategory = $package->packageCategory()->first();
              if (!empty($packageCategory)) {
                  $categoryName = Str::slug($packageCategory->name);
              } else {
                  $categoryName = "";
              }
              // dd(auth()->user()->subscription);
              $auth_subscription = auth()->check() ? auth()->user()->subscription : null;
            @endphp

            @if ((isset($auth_subscription) && $auth_subscription->status == 1) || $package->type != 'associate_to_standard_member')
              <div class="col {{ $categoryName }}">
                <div class="single-pricing-table">
                  <span class="title">{{convertUtf8($package->title)}}</span>
                  <hr style="border-color: #fff">
                  <span class="title">{{convertUtf8($package->sub_title)}}</span>
                  <hr style="border-color: #fff">
                  <p class="mb-0" style="min-height: 100px">{!! convertUtf8(nl2br($package->eligible)) !!}</p>
                  <hr style="border-color: #fff">
                  <h6 class="mb-2">{{ ('Requirements') }}:</h6>
                  <div class="features">
                    {!! replaceBaseUrl(convertUtf8($package->description)) !!}
                  </div>

                  <div class="price">
                    @if ($package->entrance_fee)
                        {{ ('Entrance Fee') }}:  {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$package->entrance_fee}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                        <br>
                        {{ ('(during application)') }}
                    @endif
                    <br>
                    <br>
                      {{ $package->packageCategory->name }}{{(' Fee')}}: {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$package->price}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                      <br>
                      @if ((isset($auth_subscription) && (isset($auth_subscription->current_package) && $auth_subscription->current_package->type == 'associate_member')) && $package->upgrade_fee > 0)
                          @lang('Upgrade Fee'):  {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$package->upgrade_fee}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                      @endif
                      @if ($bex->recurring_billing == 1)
                      @if ($package->extend_fee)
                      {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$package->extend_fee}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                      @endif
                        <small class="text-capitalize">{{$package->duration == 'monthly' ? __('Monthly') : __('Yearly')}}</small>
                      @endif
                      @if ($package->notes)
                          @lang('Notes'): {{ $package->notes }}
                      @endif
                  </div>
                  @if ($bex->recurring_billing == 1)
                    {{-- @auth
                      @if ($activeSub->count() > 0 && empty($activeSub->first()->next_package_id))
                        @if ($activeSub->first()->current_package_id == $package->id)
                          <a href="{{route('front.packageorder.index',$package->id)}}" class="pricing-btn">{{__('Current Plan')}}</a>
                        @else
                          <a href="{{route('front.packageorder.index',$package->id)}}" class="pricing-btn">{{__('Change')}}</a>
                        @endif
                      @elseif ($activeSub->count() == 0)
                        <a href="{{route('front.packageorder.index',$package->id)}}" class="pricing-btn">
                          {{($package->is_upgrade) ? __('Upgrade') : __('Register')}}
                        </a>
                      @endif
                    @endauth

                    @guest
                    <a href="{{route('front.packageorder.index',$package->id)}}" class="pricing-btn">{{($package->is_upgrade) ? __('Upgrade') : __('Register')}}</a>
                    @endguest --}}

                    @auth
                      @if ($activeSub->count() > 0)
                          @if ($activeSub->first()->current_package_id == $package->id && in_array($package->type, ['associate_member', 'standard_member']))
                              <a href="{{route('front.packageorder.index',$package->id)}}" class="pricing-btn btn-success">{{__('Extend')}}</a>
                          @endif
                          @if($package->type == 'standard_member' && (isset($auth_subscription->current_package) && $auth_subscription->current_package->type != 'standard_member') && Carbon\Carbon::parse(auth()->user()->associate_member_start_date)->age >= 3 && auth()->user()->cpd_point >= 20)
                              <a href="{{route('front.packageorder.index',$package->id)}}" class="pricing-btn">{{__('Upgared')}}</a>
                          @endif
                      {{-- @elseif ($activeSub->count() == 0 && $package->type != 'associate_to_standard_member')
                          <a href="{{route('front.packageorder.index',$package->id)}}" class="pricing-btn">{{__('Register')}}</a> --}}
                      @endif
                    @endauth

                  @else
                    @if ($package->order_status != 0)
                        @php
                            if($package->order_status == 1) {
                                $link = route('front.packageorder.index', $package->id);
                            } elseif ($package->order_status == 2) {
                                $link = $package->link;
                            }
                        @endphp

                      <a href="{{ $link }}" @if($package->order_status == 2) target="_blank" @endif class="pricing-btn">{{__('Place Order')}}</a>
                    @endif
                  @endif
                </div>
              </div>
            @endif
          @endforeach
        @endif
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>
</div>
<div class="clearfix"></div>
<!--    Packages section end   -->
@endsection

@section('scripts')
<script>
  $('#masonry-package').imagesLoaded( function() {
    // items on button click
    $('.filter-btn').on('click', 'li', function () {
      var filterValue = $(this).attr('data-filter');
      $grid.isotope({
        filter: filterValue
      });
    });
    // menu active class
    $('.filter-btn li').on('click', function (e) {
      $(this).siblings('.active').removeClass('active');
      $(this).addClass('active');
      e.preventDefault();
    });
    var $grid = $('.masonry-row').isotope({
      itemSelector: '.package-column',
      percentPosition: true,
      masonry: {
        columnWidth: 0
      }
    });
  });
</script>
@endsection
