<div id="myMovie" class="modal hide fade inverseback" tabindex="-1" role="dialog" aria-labelledby="myMovieLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="title"></h3>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <div class="span3">
                <div id="searchCover">
                        <div id="coverOverlay">
                            <center>
                                <div class="opensans">
                                    <div id="rating"></div>
                                </div>
                                <br /><br /><br /><br />
                                <div class="josefinslab">
                                    <div id="votes"></div>
                                </div>
                            </center>
                        </div>
                    <img id="searchpageCover" src="<?php echo IMAGES . '/no_cover.png'; ?>" class="img-rounded"/>
                </div>
            </div>
            <div class="span9">
                <div class="row-fluid">
                    <div class="span8 pull-left">
                        <p class="lead pline" id="genres"></p>
                    </div>
                    <div class="span1">
                        <p class="lead" id="year"></p>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span8 pull-left">
                        <p class="lead pline"><?php //$data->country; ?></p>
                    </div>
                    <div class="span3">
                        <p class="lead" id="runtime"></p>
                    </div>              
                </div>
                <div class="row-fluid">
                    <div class="span8 pull-left">
                        <p class="lead pline" id="actors"></p>
                    </div>
                </div>
                <br />
                <div class="row-fluid">
                    <div class="span8 pull-left">
                        <p class="lead pline" id="writers"></p>
                    </div>
                </div>
                <br />          
                <div class="row-fluid">
                    <div class="span9 pull-left">
                        <p id="plot"></p>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <div class="row-fluid">
            <div class="span12">
                <p>More info and options</p>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
    </div>
</div>