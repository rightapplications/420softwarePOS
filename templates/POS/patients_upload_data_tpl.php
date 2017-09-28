<?include '_header_tpl.php'?>
<style>
    .error{color:#f00; margin: 20px 0}
    .success{color:#090; margin: 20px 0}
</style>
<section class="content">
        <section class="content-header title-page for-desktop">
                <h2>UPLOAD PATIENTS DATA</h2>
        </section>
</section>

<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <button class="button" onclick="document.fileform.submit();">Upload</button>
            <button class="button" onclick="parent.location='patients.php';return false;">Cancel</button>
        </div>
    </div>	  
</section>

<section class="content">
    <div class="block-bordtop">
        <p class="p-border-left"><span></span>Upload CSV or Excel files which contain your patients' data</p>
    </div> 
</section>
<form action="<?=FILE_PORTAL_URL?>" method="post" class="singleForm" name="fileform" enctype="multipart/form-data">  
<input type="hidden" name="search_sent" value="1"/>
<input type="hidden" name="return_page" value="<?=HOST?>POS/patients_upload_data.php" />
<input type="hidden" name="client_id" value="<?=CLIENT_ID?>" />
<input type="hidden" name="client_name" value="<?=SITE_NAME?>" />
<section class="content">
    <?if(!empty($_GET['error'])){?>
    <div class="error">Sorry. Your file has not been accepted. Please check file format and try again.</div>
    <?}?>
    <?if(!empty($_GET['accepted'])){?>
    <div class="success">Thank You. Your file has been accepted. We will process it shortly.</div>
    <?}?>
    <div class="blocks-f2-repeat">
        <p>Upload file</p>
        <div class="f2-file">
            <input type="file" name="file_1"/>            
        </div>
    </div>
</section>
</form>

<?include '_footer_tpl.php'?>