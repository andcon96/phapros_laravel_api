@extends('layout.layout')

@section('content')
    {{-- Data PO --}}
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Data PO
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row col-md-12">
                    <div class="form-group row col-md-12">
                        <label for="domain" class="col-md-2 col-form-label text-md-right">{{ __('Domain') }}</label>
                        <div class="col-md-4 col-lg-3">
                            <input id="domain" type="text" class="form-control" name="domain"
                                value="{{ $receipt->getpo->po_domain ?? '' }}" readonly>
                        </div>
                        <label for="ponbr" class="col-md-2 col-form-label text-md-right">{{ __('PO Number') }}</label>
                        <div class="col-md-4 col-lg-3">
                            <input id="ponbr" type="text" class="form-control" name="ponbr"
                                value="{{ $receipt->getpo->po_nbr ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row col-md-12">
                        <label for="vendor" class="col-md-2 col-form-label text-md-right">{{ __('Vendor') }}</label>
                        <div class="col-md-4 col-lg-3">
                            <input id="vendor" type="text" class="form-control" name="vendor"
                                value="{{ $receipt->getpo->po_vend ?? '' }} - {{ $receipt->getpo->po_vend_desc ?? '' }}"
                                readonly>
                        </div>
                        <label for="shipto" class="col-md-2 col-form-label text-md-right">{{ __('Ship To') }}</label>
                        <div class="col-md-4 col-lg-3">
                            <input id="shipto" type="text" class="form-control" name="shipto"
                                value="{{ $receipt->getpo->po_ship ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row col-md-12">
                        <label for="orddate" class="col-md-2 col-form-label text-md-right">{{ __('Order Date') }}</label>
                        <div class="col-md-4 col-lg-3">
                            <input id="orddate" type="text" class="form-control" name="orddate"
                                value="{{ $receipt->getpo->po_ord_date ?? '' }}" readonly>
                        </div>
                        <label for="duedate" class="col-md-2 col-form-label text-md-right">{{ __('Due Date') }}</label>
                        <div class="col-md-4 col-lg-3">
                            <input id="duedate" type="text" class="form-control" name="duedate"
                                value="{{ $receipt->getpo->po_due_date ?? '' }}" readonly>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Data Receipt Master --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Data Receipt
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row col-md-12">
                    <div class="form-group row col-md-12">
                        <label for="rcpnbr"
                            class="col-md-4 col-form-label text-md-right">{{ __('Receipt Number') }}</label>
                        <div class="col-md-8">
                            <input id="rcpnbr" type="text" class="form-control" name="rcpnbr"
                                value="{{ $receipt->rcpt_nbr ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row col-md-12">
                        <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                        <div class="col-md-8">
                            <input id="status" type="text" class="form-control" name="status"
                                value="{{ ucfirst($receipt->rcpt_status) ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row col-md-12">
                        <label for="createdby" class="col-md-4 col-form-label text-md-right">{{ __('Created By') }}</label>
                        <div class="col-md-8">
                            <input id="createdby" type="text" class="form-control" name="createdby"
                                value="{{ $receipt->getUser->nama ?? '' }}" readonly>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Data Receipt & Checklist --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Data Checklist
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row col-md-12">
                    <div class="form-group row col-md-12">
                        <label for="imrno" class="col-md-4 col-form-label text-md-right">{{ __('IMR No.') }}</label>
                        <div class="col-md-8">
                            <input id="imrno" type="text" class="form-control" name="imrno"
                                value="{{ $receipt->getChecklist->rcptc_imr_nbr ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="artno"
                            class="col-md-4 col-form-label text-md-right">{{ __('Article No.') }}</label>
                        <div class="col-md-8">
                            <input id="artno" type="text" class="form-control" name="artno"
                                value="{{ $receipt->getChecklist->rcptc_article_nbr ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="imrdate" class="col-md-4 col-form-label text-md-right">{{ __('IMR Date') }}</label>
                        <div class="col-md-8">
                            <input id="imrdate" type="text" class="form-control" name="imrdate"
                                value="{{ $receipt->getChecklist->rcptc_imr_date ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="arrdate"
                            class="col-md-4 col-form-label text-md-right">{{ __('Arrival Date') }}</label>
                        <div class="col-md-8">
                            <input id="arrdate" type="text" class="form-control" name="arrdate"
                                value="{{ $receipt->getChecklist->rcptc_arrival_date ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="proddate"
                            class="col-md-4 col-form-label text-md-right">{{ __('Production Date') }}</label>
                        <div class="col-md-8">
                            <input id="proddate" type="text" class="form-control" name="proddate"
                                value="{{ $receipt->getChecklist->rcptc_prod_date ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="expdate"
                            class="col-md-4 col-form-label text-md-right">{{ __('Expiration Date') }}</label>
                        <div class="col-md-8">
                            <input id="expdate" type="text" class="form-control" name="expdate"
                                value="{{ $receipt->getChecklist->rcptc_exp_date ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="manu"
                            class="col-md-4 col-form-label text-md-right">{{ __('Manufacturer') }}</label>
                        <div class="col-md-8">
                            <input id="manu" type="text" class="form-control" name="manu"
                                value="{{ $receipt->getChecklist->rcptc_manufacturer ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="country" class="col-md-4 col-form-label text-md-right">{{ __('Country') }}</label>
                        <div class="col-md-8">
                            <input id="country" type="text" class="form-control" name="country"
                                value="{{ $receipt->getChecklist->rcptc_country ?? '' }}" readonly>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Data Kemasan * Transport --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Data Kemasan
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row col-md-12">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="sackdos"
                            {{ $receipt->getKemasan->rcptk_kemasan_sacdos == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="sackdos">Kemasan Sack / Dos</label>
                    </div>
                </div>
                <div class="form-group row col-md-12">
                    <label for="ketsackdos" class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                    <div class="col-md-8">
                        <input id="ketsackdos" type="text" class="form-control" name="ketsackdos"
                            value="{{ $receipt->getKemasan->rcptk_kemasan_sacdos_desc ?? '' }}" readonly>
                    </div>
                </div>

                <div class="form-group row col-md-12">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="drumvat"
                            {{ $receipt->getKemasan->rcptk_kemasan_drumvat == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="drumvat">Kemasan Drum / Vat</label>
                    </div>
                </div>
                @if ($receipt->getKemasan->rcptk_kemasan_drumvat == 1)
                    <div class="form-group row col-md-12">
                        <label for="ketdrumvat"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketdrumvat" type="text" class="form-control" name="ketdrumvat"
                                value="{{ $receipt->getKemasan->rcptk_kemasan_drumvat_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="form-group row col-md-12">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="paletpeti"
                            {{ $receipt->getKemasan->rcptk_kemasan_palletpeti == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="paletpeti">Kemasan Pallet / Peti</label>
                    </div>
                </div>
                @if ($receipt->getKemasan->rcptk_kemasan_palletpeti_desc == 1)
                    <div class="form-group row col-md-12">
                        <label for="ketpaletpeti"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketpaletpeti" type="text" class="form-control" name="ketpaletpeti"
                                value="{{ $receipt->getKemasan->rcptk_kemasan_palletpeti_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                {{-- Kondisi --}}
                <div class="form-group col-md-12 text-center">
                    <hr style="width:100%;text-align:left;margin-left:0">
                    <h5>Kondisi</h5>
                    <hr style="width:100%;text-align:left;margin-left:0">
                </div>
                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12 mb-3">
                        <h5>Bersih</h5>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_clean == 1 ? 'checked' : '' }} disabled>Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_clean == 0 ? 'checked' : '' }} disabled>No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($receipt->getKemasan->rcptk_is_clean_desc != null && $receipt->getKemasan->rcptk_is_clean_desc != '')
                    <div class="form-group row col-md-12">
                        <label for="ketisclean"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketisclean" type="text" class="form-control" name="ketisclean"
                                value="{{ $receipt->getKemasan->rcptk_is_clean_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12 mb-3">
                        <h5>Kering</h5>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_dry == 1 ? 'checked' : '' }} disabled>Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_dry == 0 ? 'checked' : '' }} disabled>No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($receipt->getKemasan->rcptk_is_dry_desc != null && $receipt->getKemasan->rcptk_is_dry_desc != '')
                    <div class="form-group row col-md-12">
                        <label for="ketisdry"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketisdry" type="text" class="form-control" name="ketisdry"
                                value="{{ $receipt->getKemasan->rcptk_is_dry_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12 mb-3">
                        <h5>Tumpah</h5>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_not_spilled == 0 ? 'checked' : '' }} disabled>Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_not_spilled == 1 ? 'checked' : '' }} disabled>No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($receipt->getKemasan->rcptk_is_not_spilled_desc != null && $receipt->getKemasan->rcptk_is_not_spilled_desc != '')
                    <div class="form-group row col-md-12">
                        <label for="ketisnotspilled"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketisnotspilled" type="text" class="form-control" name="ketisnotspilled"
                                value="{{ $receipt->getKemasan->rcptk_is_not_spilled_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                {{-- Segel --}}
                <div class="form-group col-md-12 text-center">
                    <hr style="width:100%;text-align:left;margin-left:0">
                    <h5>Segel</h5>
                    <hr style="width:100%;text-align:left;margin-left:0">
                </div>
                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_sealed == 0 ? 'checked' : '' }} disabled>Utuh
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_sealed == 1 ? 'checked' : '' }} disabled>Rusak
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Label Pabrik --}}
                <div class="form-group col-md-12 text-center">
                    <hr style="width:100%;text-align:left;margin-left:0">
                    <h5>Label Pabrik</h5>
                    <hr style="width:100%;text-align:left;margin-left:0">
                </div>
                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_manufacturer_label == 0 ? 'checked' : '' }}
                                        disabled>Utuh
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getKemasan->rcptk_is_manufacturer_label == 1 ? 'checked' : '' }}
                                        disabled>Rusak
                                </label>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Data Angkutan
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row col-md-12">
                    <label for="transporter"
                        class="col-md-3 col-form-label text-md-right">{{ __('No. Transporter') }}</label>
                    <div class="col-md-8">
                        <input id="transporter" type="text" class="form-control" name="transporter"
                            value="{{ $receipt->getTransport->rcptt_transporter_no ?? '' }}" readonly>
                    </div>
                </div>
                <div class="form-group row col-md-12">
                    <label for="transporter" class="col-md-3 col-form-label text-md-right">{{ __('No. Polis') }}</label>
                    <div class="col-md-8">
                        <input id="transporter" type="text" class="form-control" name="transporter"
                            value="{{ $receipt->getTransport->rcptt_police_no ?? '' }}" readonly>
                    </div>
                </div>

                {{-- Kondisi --}}
                <div class="form-group col-md-12 text-center">
                    <hr style="width:100%;text-align:left;margin-left:0">
                    <h5>Kondisi</h5>
                    <hr style="width:100%;text-align:left;margin-left:0">
                </div>
                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12 mb-3">
                        <h5>Bersih</h5>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_clean == 1 ? 'checked' : '' }} disabled>Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_clean == 0 ? 'checked' : '' }} disabled>No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($receipt->getTransport->rcptt_is_clean_desc != null && $receipt->getTransport->rcptt_is_clean_desc != '')
                    <div class="form-group row col-md-12">
                        <label for="ketisclean"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketisclean" type="text" class="form-control" name="ketisclean"
                                value="{{ $receipt->getTransport->rcptt_is_clean_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12 mb-3">
                        <h5>Kering</h5>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_dry == 1 ? 'checked' : '' }} disabled>Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_dry == 0 ? 'checked' : '' }} disabled>No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($receipt->getTransport->rcptt_is_dry_desc != null && $receipt->getTransport->rcptt_is_dry_desc != '')
                    <div class="form-group row col-md-12">
                        <label for="ketisdry"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketisdry" type="text" class="form-control" name="ketisdry"
                                value="{{ $receipt->getTransport->rcptt_is_dry_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12 mb-3">
                        <h5>Tumpah</h5>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_not_spilled == 0 ? 'checked' : '' }}
                                        disabled>Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_not_spilled == 1 ? 'checked' : '' }}
                                        disabled>No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if (
                    $receipt->getTransport->rcptt_is_not_spilled_desc != null &&
                        $receipt->getTransport->rcptt_is_not_spilled_desc != '')
                    <div class="form-group row col-md-12">
                        <label for="ketisnotspilled"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketisnotspilled" type="text" class="form-control" name="ketisnotspilled"
                                value="{{ $receipt->getTransport->rcptt_is_not_spilled_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                {{-- Position --}}
                <div class="form-group col-md-12 text-center">
                    <hr style="width:100%;text-align:left;margin-left:0">
                    <h5>Posisi Material</h5>
                    <hr style="width:100%;text-align:left;margin-left:0">
                </div>
                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_position_single == 0 ? 'checked' : '' }}
                                        disabled>Single
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_position_single == 1 ? 'checked' : '' }}
                                        disabled>Kombinasi
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if (
                    $receipt->getTransport->rcptt_is_position_single_desc != null &&
                        $receipt->getTransport->rcptt_is_position_single_desc != '')
                    <div class="form-group row col-md-12">
                        <label for="ketisnotspilled"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketisnotspilled" type="text" class="form-control" name="ketisnotspilled"
                                value="{{ $receipt->getTransport->rcptt_is_position_single_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                {{-- Label Pabrik --}}
                <div class="form-group col-md-12 text-center">
                    <hr style="width:100%;text-align:left;margin-left:0">
                    <h5>Segregasi</h5>
                    <hr style="width:100%;text-align:left;margin-left:0">
                </div>
                <div class="form-group col-md-12 text-center">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_segregated == 1 ? 'checked' : '' }}
                                        disabled>Yes
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input"
                                        {{ $receipt->getTransport->rcptt_is_segregated == 0 ? 'checked' : '' }} disabled>No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($receipt->getTransport->rcptt_is_segregated_desc != null && $receipt->getTransport->rcptt_is_segregated_desc != '')
                    <div class="form-group row col-md-12">
                        <label for="ketisnotspilled"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketisnotspilled" type="text" class="form-control" name="ketisnotspilled"
                                value="{{ $receipt->getTransport->rcptt_is_segregated_desc ?? '' }}" readonly>
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>

    {{-- Data Dokumen & Catatan --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Data Dokumen
                </h5>
            </div>
            <div class="card-body">

                <div class="form-group row col-md-12">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="coa"
                            {{ $receipt->getDocument->rcptdoc_is_certofanalys == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="coa">Certificate Of Analysis</label>
                    </div>
                </div>
                @if ($receipt->getDocument->rcptdoc_is_certofanalys == 1)
                    <div class="form-group row col-md-12">
                        <label for="ketcoa"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketcoa" type="text" class="form-control" name="ketcoa"
                                value="{{ $receipt->getDocument->rcptdoc_certofanalys ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="form-group row col-md-12">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="msds"
                            {{ $receipt->getDocument->rcptdoc_is_msds == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="msds">MSDS</label>
                    </div>
                </div>
                @if ($receipt->getDocument->rcptdoc_is_msds == 1)
                    <div class="form-group row col-md-12">
                        <label for="ketmsds"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketmsds" type="text" class="form-control" name="ketmsds"
                                value="{{ $receipt->getDocument->rcptdoc_msds ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="form-group row col-md-12">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="forwarderdo"
                            {{ $receipt->getDocument->rcptdoc_is_forwarderdo == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="forwarderdo">Forwarder DO</label>
                    </div>
                </div>
                @if ($receipt->getDocument->rcptdoc_is_forwarderdo == 1)
                    <div class="form-group row col-md-12">
                        <label for="ketforwarderdo"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketforwarderdo" type="text" class="form-control" name="ketforwarderdo"
                                value="{{ $receipt->getDocument->rcptdoc_forwarderdo ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="form-group row col-md-12">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="packinglist"
                            {{ $receipt->getDocument->rcptdoc_is_packinglist == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="packinglist">Packing List</label>
                    </div>
                </div>
                @if ($receipt->getDocument->rcptdoc_is_packinglist == 1)
                    <div class="form-group row col-md-12">
                        <label for="ketpackinglist"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketpackinglist" type="text" class="form-control" name="ketpackinglist"
                                value="{{ $receipt->getDocument->rcptdoc_packinglist ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

                <div class="form-group row col-md-12">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="otherdocs"
                            {{ $receipt->getDocument->rcptdoc_is_otherdocs == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="otherdocs">Other Docs</label>
                    </div>
                </div>
                @if ($receipt->getDocument->rcptdoc_is_otherdocs == 1)
                    <div class="form-group row col-md-12">
                        <label for="ketotherdocs"
                            class="col-md-3 col-form-label text-md-right">{{ __('Keterangan') }}</label>
                        <div class="col-md-8">
                            <input id="ketotherdocs" type="text" class="form-control" name="ketotherdocs"
                                value="{{ $receipt->getDocument->rcptdoc_otherdocs ?? '' }}" readonly>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Catatan
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row col-md-12">
                    <label for="rcpnbr" class="col-md-4 col-form-label text-md-right">{{ __('Catatan') }}</label>
                    <div class="col-md-8">
                        <input id="rcpnbr" type="text" class="form-control" name="rcpnbr"
                            value="{{ $receipt->getTransport->rcptt_angkutan_catatan ?? '' }}" readonly>
                    </div>
                </div>
                <div class="form-group row col-md-12">
                    <label for="rcpnbr" class="col-md-4 col-form-label text-md-right">{{ __('Kelembapan') }}</label>
                    <div class="col-md-8">
                        <input id="rcpnbr" type="text" class="form-control" name="rcpnbr"
                            value="{{ $receipt->getTransport->rcptt_kelembapan ?? '' }}" readonly>
                    </div>
                </div>

                <div class="form-group row col-md-12">
                    <label for="rcpnbr" class="col-md-4 col-form-label text-md-right">{{ __('Suhu') }}</label>
                    <div class="col-md-8">
                        <input id="rcpnbr" type="text" class="form-control" name="rcpnbr"
                            value="{{ $receipt->getTransport->rcptt_suhu ?? '' }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data File Upload --}}
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    File Upload
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row col-md-12">
                    <div class="col-lg-10 offset-lg-1">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 table-striped"
                                style="overflow: scroll; white-space:nowrap;">
                                <thead>
                                    <tr>
                                        <th width="12%">Image</th>
                                        <th width="12%">Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($receipt->getFileUpload as $key => $row)
                                        <tr>
                                            <td style="text-align: center">
                                                <a href="{{asset($row->rcptfu_path)}}" target="_blank">
                                                    <img src="{{asset($row->rcptfu_path)}}" height="120" width="120" alt="">
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{route('downloadFileReceipt', ['id' => $row->id])}}" target="_blank">
                                                    <i class="fa fa-download color-primary"></i>
                                                </a>
                                                
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Detail --}}
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Data Detail Receipt
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group row col-md-12">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 table-striped"
                                style="overflow: scroll; white-space:nowrap;">
                                <thead>
                                    <tr>
                                        <th width="12%">Line</th>
                                        <th width="25%">Part</th>
                                        <th width="12%">UM</th>
                                        <th width="12%">Qty Per Package</th>
                                        <th width="12%">Qty Arrival</th>
                                        <th width="12%">Qty Approved</th>
                                        <th width="12%">Qty Reject</th>
                                        <th width="12%">Location</th>
                                        <th width="12%">Lot</th>
                                        <th width="12%">Batch</th>
                                        <th width="12%">Expiration Date</th>
                                        <th width="12%">Manufacturer Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($receipt->getDetail as $key => $row)
                                        <tr>
                                            <td>{{ $row->rcptd_line }}</td>
                                            <td>{{ $row->rcptd_part }} - {{ $row->rcptd_part_desc }}</td>
                                            <td>{{ $row->rcptd_part_um }}</td>
                                            <td>{{ $row->rcptd_qty_per_package }}</td>
                                            <td>{{ $row->rcptd_qty_arr }}</td>
                                            <td>{{ $row->rcptd_qty_appr }}</td>
                                            <td>{{ $row->rcptd_qty_rej }}</td>
                                            <td>{{ $row->rcptd_loc }}</td>
                                            <td>{{ $row->rcptd_lot }}</td>
                                            <td>{{ $row->rcptd_batch }}</td>
                                            <td>{{ $row->rcptd_exp_date }}</td>
                                            <td>{{ $row->rcptd_manu_date }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <a href="{{route('purchaseorder.index')}}" class="btn btn-outline-secondary"><span class="btn btn-icon-left text-secondary"><i
                        class="fa fa-arrow-left"></i>
                </span>Back</a>
                </div>
            </div>
        </div>
    </div>


    <div id="loader" class="lds-dual-ring hidden overlay"></div>
@endsection
