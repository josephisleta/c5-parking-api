<?php defined('C5_EXECUTE') || die("Access Denied.");

Core::make('help')->display("Parking Settings");

if (isset($notify)) {
    echo Core::make('helper/concrete/ui')->notify($notify);
}
?>

<div class="ccm-ui">
    <div class="row">
        <form method="post" id="diversity-survey-toggle" class="form-horizontal">
            <div class="columns col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="entry-exit-points">Number of Entry/Exit points</label>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <input id="entry-exit-points" type="number" name="entry-exit-points" min="3" value="<?php echo $numberOfEntryExitPoints ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="parking-slots">Parking Slots (JSON)</label>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <textarea id="parking-slots" name="parking-slots" rows="20" cols="100"> <?php echo $parkingSlots ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <input name="btnSubmit" id="btnSubmit" class="btn btn-primary" value="Save Changes" type="submit" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>