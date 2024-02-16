<!-- Details Modal -->
<div class="modal fade" id="detailsModal{{$sub->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
              <div class="row">
                  <div class="col-lg-4">
                      <strong style="text-transform: capitalize;">Name:</strong>
                  </div>
                  <div class="col-lg-8">{{$sub->name}}</div>
              </div>
              <hr>
              <div class="row">
                  <div class="col-lg-4">
                      <strong style="text-transform: capitalize;">Email:</strong>
                  </div>
                  <div class="col-lg-8">{{$sub->email}}</div>
              </div>
              <hr>
            @php
              $fields = json_decode($sub->fields, true);
              // dd($fields)
            @endphp

            @foreach ($fields as $key => $field)
            <div class="row">
              <div class="col-lg-4">
                <strong style="text-transform: capitalize;">{{str_replace("_"," ",$key)}}:</strong>
              </div>
              <div class="col-lg-8">
                  @if (is_array($field['value']))
                      @php
                          $str = implode(", ", $field['value']);
                      @endphp
                      {{$str}}
                  @else
                      @if ($field['type'] == 5)
                          <a href="{{asset('assets/front/files/' . $field['value'])}}" class="btn btn-primary btn-sm" download="{{$key . ".zip"}}">Download</a>
                      @else
                          {{$field['value']}}
                      @endif
                  @endif
              </div>
            </div>
            <hr>
            @endforeach

            @if (request()->input('type') != 'request')
                @if ($sub->current_package)
                    <div class="row">
                        <div class="col-lg-4">
                            <strong class="d-inline-block py-1">Current Membership:</strong>
                        </div>
                        <div class="col-lg-8">
                            <div id="curpack{{$sub->id}}">
                                <span class="d-inline-block py-1">{{ $sub->current_package->title }}</span>
                                <button class="btn btn-secondary btn-sm p-1 ml-3" onclick="$('#curpack{{$sub->id}}').hide();$('#edcurpac{{$sub->id}}').show();">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            </div>
                            <div id="edcurpac{{$sub->id}}" style="display:none">
                                <form action="{{ route('admin.subs.change_package', ['id' => $sub->id]) }}" method="POST">
                                    @csrf {{-- csrf token field --}}
                                    <div class="form-inline">
                                        <select name="package_id" id="package_id" class="form-control py-1 px-0">
                                            @foreach ($packages as $package)
                                            <option value="{{$package->id}}" {{ $sub->current_package_id == $package->id ? 'selected' : ''}}>{{ $package->title }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm p-1 ml-3" onclick="return confirm('Are you sure?')">
                                            <i class="fa fa-save"></i> Save
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm p-1 ml-1" onclick="$('#edcurpac{{$sub->id}}').hide();$('#curpack{{$sub->id}}').show();">
                                            <i class="fa fa-danger"></i> Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Current Membership Price:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}
                            {{ $sub->is_upgrade == 0 ? $sub->current_package->price : $sub->current_package->upgrade_fee }}
                            {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                            @if ($sub->is_upgrade == 1)
                                (@lang('Upgrade Fee'))
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Payment Method:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$sub->current_payment_method}}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Invoice:</strong>
                        </div>
                        <div class="col-lg-8">
                            @if(!file_exists(root_path('assets/front/invoices/' . $sub->invoice)))
                            <form action="{{ route('user-invoice', ['paymentid' => encrypt($sub->id)]) }}"
                                method="post">@csrf
                                <input type="hidden" name="model" value="payments">
                                <button type="submit" class="btn btn-primary btn-sm">{{('INVOICE')}}</button>
                            </form>
                            @else
                            <form action="{{ asset('assets/front/invoices/' . $sub->invoice) }}" method="get">
                                <button type="submit" class="btn btn-primary btn-sm">INVOICE</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <hr>
                @endif

                @if ($sub->next_package)

                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Next / Upcoming Membership:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{ $sub->next_package->title }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Next Membership Price:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}
                            {{ $sub->is_upgrade == 0 ? $sub->next_package->price : $sub->next_package->upgrade_fee }}
                            {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                            @if ($sub->is_upgrade == 1)
                                (@lang('Upgrade Fee'))
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Next Membership Payment Method:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$sub->next_payment_method}}
                        </div>
                    </div>
                    <hr>
                @endif

                <div class="row">
                    <div class="col-lg-4">
                      <strong>Status:</strong>
                    </div>
                    <div class="col-lg-8">
                        @if ($sub->status == 1)
                            <span class="badge badge-success">Active</span>
                        @elseif ($sub->status == 0)
                            <span class="badge badge-danger">Expired</span>
                        @endif
                    </div>
                  </div>
                  <hr>
            @else

                @if ($sub->pending_package)
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Pending Package:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{ $sub->pending_package->title }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Pending Package Price:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}
                            {{ $sub->is_upgrade == 0 ? $sub->pending_package->price : $sub->pending_package->upgrade_fee }}
                            {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                            @if ($sub->is_upgrade == 1)
                                (@lang('Upgrade Fee'))
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <strong>Pending Membership Payment Method:</strong>
                        </div>
                        <div class="col-lg-8">
                            {{$sub->pending_payment_method}}
                        </div>
                    </div>
                    <hr>
                @endif
            @endif

            <div class="row">
                <div class="col-lg-4">
                    <strong>Document Files:</strong>
                </div>
                <div class="col-lg-8">
                    @php
                        $document_files = ($sub->document_file) ? json_decode($sub->document_file,true) : [];
                        // dd($document_files);
                    @endphp
                    <div class="d-block">
                        @foreach ($document_files as $key => $document_file)
                            @php
                                $file_name = key($document_file);
                                $doc = asset('assets/front/document/'.$document_file[$file_name]);
                            @endphp
                            <div class="mb-2">
                                <h4>{{ $file_name }}</h4>
                                <a href="{{ $doc }}" target="_blank">
                                    <img src="{{ $doc }}" width="200" alt="">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#edcurpac{{$sub->id}}').hide();$('#curpack{{$sub->id}}').show();">Close</button>
        </div>
      </div>
    </div>
  </div>
