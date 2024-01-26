{include file="sections/header.tpl"}

		<div class="row">
			<div class="col-sm-12 col-md-12">
				<div class="panel panel-primary panel-hovered panel-stacked mb30">
					<div class="panel-heading">{$_L['Add_Plan']}</div>
						<div class="panel-body">
                        <form class="form-horizontal" method="post" role="form" action="{$_url}services/balance-add-post" >
                            <div class="form-group">
                                <label class="col-md-2 control-label">{Lang::T('Status')}</label>
                                <div class="col-md-10">
                                    <label class="radio-inline warning">
                                        <input type="radio" checked name="enabled" value="1"> Enable
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="enabled" value="0"> Disable
                                    </label>
                                </div>
                            </div>
														<div class="form-group">
																<label class="col-md-2 control-label">{Lang::T('Client Can Purchase')}</label>
																<div class="col-md-10">
																		<label class="radio-inline warning">
																				<input type="radio" checked name="allow_purchase" value="yes"> Yes
																		</label>
																		<label class="radio-inline">
																				<input type="radio" name="allow_purchase" value="no"> No
																		</label>
																</div>
														</div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">{$_L['Plan_Name']}</label>
                                <div class="col-md-6">
                                    <input type="text" required class="form-control" id="name" name="name" maxlength="40" placeholder="Topup 100">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">{$_L['Plan_Price']}</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">{$_c['currency_code']}</span>
                                        <input type="number" class="form-control" name="price" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button class="btn btn-success waves-effect waves-light" type="submit">{$_L['Save']}</button>
                                    Or <a href="{$_url}services/balance">{$_L['Cancel']}</a>
                                </div>
                            </div>
                        </form>
					</div>
				</div>
			</div>
		</div>

{include file="sections/footer.tpl"}
