{include file="sections/header.tpl"}
<!-- voucher-add -->

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading">{$_L['Add_Voucher']}</div>
            <div class="panel-body">

                <form class="form-horizontal" method="post" role="form" action="{$_url}prepaid/voucher-post">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{$_L['Type']}</label>
                        <div class="col-md-6">
                            <input type="radio" id="Hot" name="type" value="Hotspot"> {$_L['Hotspot_Plans']}
                            <input type="radio" id="POE" name="type" value="PPPOE"> {$_L['PPPOE_Plans']}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{$_L['Routers']}</label>
                        <div class="col-md-6">
                            <select id="server" name="server" class="form-control select2">
                                <option value=''>{$_L['Select_Routers']}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{$_L['Service_Plan']}</label>
                        <div class="col-md-6">
                            <select id="plan" name="plan" class="form-control select2">
                                <option value=''>{$_L['Select_Plans']}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{$_L['Number_of_Vouchers']}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="numbervoucher" value="1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Voucher Format')}</label>
                        <div class="col-md-6">
                            <select name="voucher_format" id="voucher_format" class="form-control">
                                <option value="up" {if $_c['voucher_format'] == 'up'}selected="selected" {/if}>UPPERCASE
                                </option>
                                <option value="low" {if $_c['voucher_format'] == 'low'}selected="selected" {/if}>
                                    lowercase
                                </option>
                                <option value="rand" {if $_c['voucher_format'] == 'rand'}selected="selected" {/if}>
                                    RaNdoM
                                </option>
                            </select>
                        </div>
                        <p class="help-block col-md-4">UPPERCASE lowercase RaNdoM</p>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Voucher Prefix')}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="prefix" placeholder="NUX-" value="{$_c['voucher_prefix']}">
                        </div>
                        <p class="help-block col-md-4">NUX-VoUCHeRCOdE</p>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{$_L['Length_Code']}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="lengthcode" value="12">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success waves-effect waves-light"
                                type="submit">{$_L['Generate']}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}