







<script>

    var isforcedupdate = false;


    const uripars = {};
    window.location.search
        .substring(1)
        .split("&")
        .forEach(param => {
            let [key, value] = param.split("=");
            if (key) {
                uripars[decodeURIComponent(key)] = value ? decodeURIComponent(value) : "";
            }
        });

    console.log("params:");
    console.log(uripars); // Debug: Prints extracted parameters as an object




    function capFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }


    function selectText(containerid) {


        console.log('selectText id: ' + containerid);

        if (document.selection) { // IE
            var range = document.body.createTextRange();
         //   range.moveToElementText(document.getElementById(containerid));
            range.moveToElementText(document.querySelector(containerid));
            range.select();
        } else if (window.getSelection) {
            var range = document.createRange();
          //  range.selectNode(document.getElementById(containerid));
            range.selectNode(document.querySelector(containerid));
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
        }
    }

    // copy content to clipboard
    function copyInst(sel,type){
        /* Get the text field */
        var copyText = $(sel);

        let copyval = '';
        if(type==='input') {
            copyval = $(copyText).val();
            copyText.select();
        }
        else{
            copyval = $(copyText).text();
            selectText(sel);
        }

        console.log('value=' + copyval);


        /* Select the text field */

        document.execCommand('copy');

        let unfocustar = sel + 'unfocus';
        selectText(unfocustar);
        doTooltipToggle(copyText);

        return true;

    }


    function doTooltipToggle(el){

        let toolt = $('.tooltip-inner');
        $(toolt).html('Copied!');



    }

    function checkURL(url) {
        var re = /[a-z0-9-\.]+\.[a-z]{2,4}\/?([^\s<>\#%"\,\{\}\\|\\\^\[\]`]+)?$/;
        re = /^(ftp|http|https|chrome|:\/\/|\.|@){2,}(localhost|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|\S*:\w*@)*([a-zA-Z]|(\d{1,3}|\.){7}){1,}(\w|\.{2,}|\.[a-zA-Z]{2,3}|\/|\?|&|:\d|@|=|\/|\(.*\)|#|-|%)*$/gum
        if (!re.test(url)) {
            console.log('invalid url - ' + url);
            $('#previewframe').parent().addClass('hidr');
            return false;
        }
        else{
            console.log('valid url - ' + url);
            $('#previewframe').attr('src','_html-preview-display.php?url=' + url);
            $('#previewframe').parent().removeClass('hidr');
        }
    }

    function uNotify(align,mtype,mtitle,mess){

        let offsetamt = 0;
        let direction = 'bottom';
        let algnment = 'right';



        if(align==='center'){
            offsetamt = 100;
            direction = 'top';
            algnment = 'center';
        }

        if(align==='bottom'){
            offsetamt = 100;
            direction = 'top';
            algnment = 'right';
        }


        if(align==='default'){
            offsetamt = 100;
            direction = 'top';
            algnment = 'center';
        }



        console.log('align: ' + align + ' alignment:' + algnment);

        $.bootstrapGrowl('<strong>' + mtitle + ' </strong>' + mess + " &nbsp;",{
            type: mtype,
            offset: {from: direction, amount: offsetamt},
            align: algnment,
            width: 'auto',
            delay: 3000,
            stackup_spacing: 10
        });
    }

    function prepFieldMessage(fieldsarray){
        let str = '';
        let fieldcnt = 0;
        let fieldarray;
        for(var k in fieldsarray) {
            if (fieldsarray.hasOwnProperty(k)) {
                fieldarray = fieldsarray[k];
                console.log('prepFieldMessage: ' + JSON.stringify(fieldarray));
                console.log('prepFieldMessage field: form [name="' + fieldarray.field + '"]');
                str += fieldarray.label + ': ' + fieldarray.message + " <br>";
                let el = $('form [name="' + fieldarray.field + '"]');
                let labl = (fieldarray.field != 'resp') ? (($(el).attr('aria-label')) ? $(el).attr('aria-label') : '')  : '';
                if(!(labl)||labl==''){
                    labl = $(el).parent().parent().find('.label').text();
                    if(labl==''){
                        labl = fieldarray.label;
                    }
                }

                let msg = (fieldarray.message === 'Missing') ? ' Required' : ' ' + fieldarray.message
                let combinederr = (fieldarray.field==='state' && fieldarray.message!=='Missing') ? msg : labl + msg;
                if(fieldarray.field==='terms'){
                    $(el).parent().parent().parent().find('.jsonform-errortext').text('Be sure you’ve reviewed and agree with this information before you submit.');
                    $(el).parent().parent().parent().addClass('errborder');
                }
                else{
                    $(el).parent().find('.jsonform-errortext').text(combinederr);
                    $(el).parent().addClass('errborder');
                }

            }
            fieldcnt++;
        }
        return (str!='') ? '<br>' + str : '';
    }

    var thefieldarray = '';
    function execAjax(formid,upvals,redirURL){

        $.post('_process_form.php', {formid: formid, upvals: upvals}, function (response) {
            var data = jQuery.parseJSON(response);

            var retr = data.mess;
            var formredir = '';
            var redir = (data.redirection != null) ? ((data.redirection !== false) ? data.redirection : formredir) : formredir ;
            var extrainfo = data.extrainfo;
            var fieldarray = data.fieldarray;
          //  thefieldarray = fieldarray;
            var dbg= data.dbg;
            console.log("execAjax(" + formid + "," + upvals + "," + redirURL + ")");
            console.log("return:  \ndata.mess = "+ retr +" \ndata.success = " + data.success + " \ndata.redirection = " + redir + " \ndata.fieldarray = " + JSON.stringify(fieldarray) + " \ndata.dbg = " + dbg);

            if (data.success) {
                if(redir!=''){
                    location.href = redir;
                }
                else if(redir=='none'){
                    console.log('redirection cancelled');
                }
                else{
                    if(formid.indexOf('deliverform')>-1){

                        let dispform = $('#' + formid);
                        let container = $(dispform).find('.inventorygroup');
                        let input = $(container).find('input');
                        let button = $(container).find('.genformbutton');


                        $(container).addClass('groupcomplete');
                        $(input).attr('readonly',true);
                        $(button).prop('disabled',true);
                        $(button).addClass('disabled');




                        $('.modal-message button.close').click();
                      //  uNotify('default','success','Success',retr);
                    }
                    else if(formid.indexOf('receivingform')>-1){

                        let dispform = $('#' + formid);
                        let container = $(dispform).find('.inventorygroup');
                        let input = $(container).find('input');
                        let button = $(container).find('.genformbutton');


                        $(container).addClass('groupcomplete');
                        $(input).attr('readonly',true);
                        $(button).prop('disabled',true);
                        $(button).addClass('disabled');




                        $('.modal-message button.close').click();
                       // uNotify('default','success','Success',retr);
                    }
                    else if(formid==='finalizedelivery'||formid==='finalizereceiving'){


                        $('.notesholdr').addClass('groupcomplete');
                        $('button.finalizedata').addClass('disabled');
                        $('.modal-message button.close').click();

                        $('.showsuccessholdr').removeClass('hidr');


                        uNotify('default','success','Success','<br><em>' + retr + '</em>');

                    }
                    else{
                        $('.modal-message button.close').click();
                        uNotify('default','success','Success','<br><em>' + retr + '</em>');
                    }
                }
            }
            else {
                console.log(formid + ' failure registered - data.mess = ' + retr);
                if(formid==='profileform' || formid==='editprofileform'){
                    if(retr.substring(0,13)==='unknown error'){
                        uNotify('default','warning','No action taken',"No data has been modified.  <br>Please edit user application fields before saving changes.");
                    }
                    else{
                        console.log('growl init');
                        let errhead = 'Error';
                        let err = retr;
                        if(fieldarray!=null && fieldarray!=''){
                            errhead = fieldarray[0]['label'];
                            err =  fieldarray[0]['message'];
                        }
                        let str = retr.replace('formid-' + formid, '');
                        uNotify('default','danger',errhead, err);
                    }
                }
                else if(formid==='phonelookup'){
                    console.log('extrainfo-' + extrainfo);

                    $('[name="isverified"]').val('1');
                    $('[name="bestphone_0"]').parent().find('span.jsonform-errortext').text(retr);
                    $('[name="bestphone_0"]').parent().removeClass('verifiedborder');
                    $('[name="bestphone_0"]').parent().addClass('errborder');
                    // uNotify('default','success','Success','Platform Configuration Saved!');
                }
                else {
                    let str = retr.replace('!data.fail->ormid-' + formid, '');
                    let errmess = prepFieldMessage(fieldarray);
                    uNotify('default','danger', 'Error', str + '<br>' + errmess);
                }
            }

            // RESET LOADING BUTTONS
            $('.btn-disabled').attr('disabled',false);
            $('.btn-disabled').removeClass('btn-disabled');

        });
    }

    function dynSubmit(formid,theform,redirURL){

        console.log('form submission for ' + formid);
        var values = {};
        $.each($(theform).serializeArray(), function(i, field) {
            values[field.name] = field.value;
            console.log(field.name + ' = ' + field.value);
        });
        let upvals = JSON.stringify(values);
        console.log("onclick final Submit sending " + upvals);
        execAjax(formid,upvals,redirURL);

    }


    function dynReportValSubmit(formid,theform,redirURL){

        console.log('form submission for ' + formid);
        var values = {};
        $.each($(theform).serializeArray(), function(i, field) {
            values[field.name] = field.value;
            console.log(field.name + ' = ' + field.value);
        });
        let upvals = JSON.stringify(values);
        console.log("onclick final Submit sending " + upvals);
        execAjax(formid,upvals,redirURL);

    }








    function modalIframe(iframeloc,action){
        console.log('modalIframe');

        let newloc = 'includes/phpqrcode/index-edit.php' + iframeloc;

        $('#qrresults').attr('src',newloc);

        $('#modal-qrcode').modal(action);
    }


    var primarysite = '';
    var primarymode = false;

    function calcTotalsTable(){


        let irstr = '#choosevendor .table.reporttable.totalstable';
        console.log('calcTotalsTable totstable targets: ' + irstr);
        let ir = $(irstr);


        let rows = $(ir).find('.siteinvresultrow:not(.totalsrow)');
        let totrow = $(ir).find('.siteinvresultrow.totalsrow');


        let delfintottar = $(totrow).find('.sitetotal.pair1');
        let retfintottar = $(totrow).find('.sitetotal.returned');
        let damfintottar = $(totrow).find('.sitetotal.damaged');

        let fintottar = $(totrow).find('.sitetotal.rowtotal');


        let del = 0;
        let ret = 0;
        let dam = 0;

        $(rows).each(function() {

            let rowdel = $(this).find('.balance.pair1').text() * 1;
            let rowret = $(this).find('.balance.returned').text() * 1;
            let rowdam = $(this).find('.balance.damaged').text() * 1;
            let rowtot = $(this).find('.balance.rowtotal');


            del = del + rowdel;
            console.log('calcTotalsTable del:' + del);
            ret = ret + rowret;
            console.log('calcTotalsTable ret:' + ret);
            dam = dam + rowdam;
            console.log('calcTotalsTable ret:' + ret);


            let finalval = rowdel - rowret - rowdam;
            finalval = finalval * -1;
            console.log('calcTotalsTable rowtotal: ' + finalval);
            $(rowtot).text(finalval + '');

        })

        let finaltot = del - ret - dam;
        finaltot = finaltot * -1;
        $(delfintottar).text(del + '');
        $(retfintottar).text(ret + '');
        $(damfintottar).text(dam + '');
        $(fintottar).text(finaltot + '');



    }

    function calcWeeklySiteTotals(table,sitename){

        let ir = $('#' + table);
        let totval = 0;


        let sitetocolsstr = '.siteinvresultrow[data-sitename="' + sitename + '"]:not(.totalsrow) .balance.returned';
        console.log('calcWeeklySiteTotals sitetocolsstr: ' + sitetocolsstr);
        let sitetotcols = $(ir).find(sitetocolsstr);
        $(sitetotcols).each(function(){

            let val = $(this).text() * 1;
            totval += val;
            console.log('calcWeeklySiteTotals totval:' + totval);

        })




        let weeklysitetotstr = 'tr[data-sitename="' + sitename + '"].totalsrow .balance';
        console.log('calcWeeklySiteTotals weeklysitetotstr: ' + weeklysitetotstr);
        $(ir).find(weeklysitetotstr).text(totval);

    }


    function calcTotalAllMonthly(){






        let prodcols = $('.reporttable.totalstable .siteinvresultrow:not(.totalsrow) > td:first-of-type');

        $(prodcols).each(function(){

            let product = $(this).text();

            let primarydeladd = (primarymode) ? '[data-sitename="' + primarysite + '"]' : '';
            let primaryretadd = (primarymode) ? ':not([data-sitename="' + primarysite + '"])' : '';




            let deltotstr = '#choosevendor .table.reporttable.totalstable .siteinvresultrow[data-product="' + product + '"] .balance.pair1';
            console.log('calcTotalAllMonthly delivered totals target: ' + deltotstr);
            let deltottar = $(deltotstr);

            //get target for grand returns total for current product
            let rettotsstr = '#choosevendor .table.reporttable.totalstable .siteinvresultrow[data-product="' + product + '"] .balance.returned';
            console.log('calcTotalAllMonthly returns totals target: ' + rettotsstr);
            let rettottar = $(rettotsstr);


            let delstr = '#choosevendor .table.reporttable:not(.totalstable) .siteinvresultrow[data-product="' + product + '"]' + primarydeladd + ' .pair1';
            console.log('calcTotalAllMonthly delivered columns selector:' + delstr);
            let dels = $(delstr);
            console.log('calcTotalAllMonthly delivered cols:' + dels);


            let retstr = '#choosevendor .table.reporttable:not(.totalstable) .siteinvresultrow[data-product="' + product + '"]' + primaryretadd + ' .returned:not(.balance)';
            console.log('calcTotalAllMonthly returned columns selector:' + retstr);
            let rets = $(retstr);
            console.log('calcTotalAllMonthly delivered cols:' + rets);



            let del = 0;
            let ret = 0;

            $(dels).each(function(){

                let val = $(this).text() * 1;
                del += val;
              //  console.log('calcTotalAllMonthly del:' + del);
            })

            $(rets).each(function(){

                let val = $(this).text() * 1;
                ret += val;
             //   console.log('calcTotalAllMonthly ret:' + ret);

            })




            console.log('calcTotalAllMonthly delivered total for ' + product + ':' + del);
            console.log('calcTotalAllMonthly returned total for ' + product + ':' + ret);

            $(deltottar).text(del + '');
            $(rettottar).text(ret + '');





        })




        //calculate out final grand totals (totals table
        calcTotalsTable();










    }






    function calcMonthlyTotals(sitename,product,isprimarymode){






        let primaryadd = (isprimarymode) ? '[data-sitename="' + sitename + '"]' : '';
        console.log('calcMonthlyTotals primaryadd: ' + primaryadd);
        //get target for grand delivery total for current product

        let deltotstr = '#choosevendor .table.reporttable.totalstable .siteinvresultrow' + primaryadd + '[data-product="' + product + '"] .balance.pair1';
        console.log('calcMonthlyTotals delivered totals target: ' + deltotstr);
        let deltottar = $(deltotstr);

        //get target for grand returns total for current product
        let rettotsstr = '#choosevendor .table.reporttable.totalstable .siteinvresultrow[data-product="' + product + '"] .balance.returned';
        console.log('calcMonthlyTotals returns totals target: ' + rettotsstr);
        let rettottar = $(rettotsstr);

        //counters for all deliveries / returns for given prodcut - across all tables
        let deltots = 0;
        let rettots = 0;

        let ir = $('#choosevendor > .table-responsive.scrollbar');
        let tables = $(ir).find('table.reporttable:not(.totalstable)');


        //loop through each table
        $(tables).each(function(){

            let del = 0;
            let ret = 0;
            let thissite = '';
            let leTableID = $(this).attr('id');
            console.log('calcMonthlyTotals leTableID: ' + leTableID);

         //   let primeretrows

            let rowstr = '.siteinvresultrow[data-product="' + product + '"]';
            console.log('calcMonthlyTotals product target row: ' + rowstr);
            let rows = $(this).find(rowstr);

            let tabletotcolsstr = '.siteinvresultrow[data-sitename="' + sitename + '"] .balance.returned';
            console.log('calcMonthlyTotals totals target cols: ' + tabletotcolsstr);
            let totcols = $(this).find(tabletotcolsstr);

            let tabletotsstr = '.siteinvresultrow[data-sitename="' + sitename + '"].totalsrow .balance';
            console.log('calcMonthlyTotals table totals: ' + tabletotsstr);
            let tabletotal = $(this).find(tabletotsstr);

            //get product totals for current table
            $(rows).each(function(){

                del = 0;
                ret = 0;

                thissite = $(this).attr('data-sitename');

                let dels = $(this).find('.pair1');
                let rets = $(this).find('.returned:not(.balance)');
                let bal = $(this).find('.balance.returned');

              //  let tot = $(this).find('.balance.returned');

               let iscurrsitemain = (thissite==primarysite);

                $(dels).each(function(){

                    let val = $(this).text() * 1;
                    del += val;
                    console.log('calcMonthlyTotals del:' + del);
                })

                $(rets).each(function(){

                    let val = $(this).text() * 1;
                    ret += val;
                    console.log('calcMonthlyTotals ret:' + ret);

                })

                if((isprimarymode===true && iscurrsitemain===true) || isprimarymode===false) {

                    deltots += del;
                }

                if((isprimarymode===true && iscurrsitemain===false) || isprimarymode===false) {
                    rettots += ret;
                }

                let finalrowval = del - ret;
                $(bal).text(finalrowval + '');   //write final row total to balance column

                calcWeeklySiteTotals(leTableID,thissite);

            })

        })


        //write the product totals (combined from all tables) to the final totals table
        $(deltottar).text(deltots + '');
        $(rettottar).text(rettots + '');

        //calculate out final grand totals (totals table
        calcTotalsTable();


    }



    function updateReportDBVal(thethis){

        let = tdval = $(thethis).text();
        let entrydate = $(thethis).attr('data-sub-entrydate');
        let formid = $(thethis).attr('data-sub-formid');

     /*
        let isdelivered = $(thethis).hasClass('.pair1');
        let isreturned = $(thethis).hasClass('.returned');

        let dtype = (isdelivered==true) ? 'deliver' : 'receiving';
*/




        let ir = $(thethis).closest('.siteinvresultrow');
		let invname = $(ir).attr('data-sub-invname');



        var values = {};
        values['siteid'] = $(ir).attr('data-sub-siteid');
        values['vendorid'] = $(ir).attr('data-sub-vendorid');
        values['is_' + invname] = '1';
        values['entrydate'] = entrydate;

        if($(thethis).hasClass('damaged')){
            values[invname + '_dmg'] = tdval;
            values['is_report_damage'] = '1';
            console.log('damaged value from report');
        }
        else{
            values[invname + '_amt'] = tdval;
            console.log('not damaged value');

        }


        let upvals = JSON.stringify(values);
        console.log("updateReportDBVal final Submit sending " + upvals);
        execAjax(formid,upvals,'none');

     //   data-sub-is_3_comp_clamshells





    }








    function calcBodyMainTotals(thethis,isprimarymode){

        let = tdval = $(thethis).text();
        console.log('calcBodyMainTotals change ' + tdval);

        let ir = $(thethis).closest('.siteinvresultrow');

        let sitename = $(ir).attr('data-sitename');
        console.log('calcBodyMainTotals sitename: ' + sitename);
        let prodname = $(ir).attr('data-product');
        console.log('calcBodyMainTotals prodname: ' + prodname);


        let bal = $(ir).find('.balance.returned');

        let del = 0;
        let ret = 0;

        let dels = $(ir).find('.pair1');
        let rets = $(ir).find('.returned:not(.balance)');

        $(dels).each(function(){

            let val = $(this).text() * 1;
            del = del + val;
            console.log('calcBodyMainTotals del:' + del);

        })

        $(rets).each(function(){

            let val = $(this).text() * 1;
            ret = ret + val;
            console.log('calcBodyMainTotals ret:' + ret);

        })
        let finalval = del - ret;
        finalval = finalval * -1;
        let stval = finalval + "";
        $(bal).text(stval);


        let leTableID = $(ir).closest('table').attr('id');

     //   calcMonthlyTotals(sitename,prodname,isprimarymode);


        calcWeeklySiteTotals(leTableID,sitename);
        calcTotalAllMonthly();

    }



    function calcDailyPrimaryReturnVals(){



        let tabls = $('#report-monthly table.table.reporttable:not(.totalstable)');
        $(tabls).each(function(){

            let tblid = $(this).attr('id');
            console.log('table: ' + tblid);

            let prodcut = '';
            let proddate = "";

            //get all primary site rows
            let prodrows = $(this).find('tr.siteinvresultrow.isprimarysite');
            $(prodrows).each(function(){


                //isolate the product
                product = $(this).attr('data-sub-invname');
          //      console.log('product: ' + product);

                //get the main site return display field
                let retovd = $(this).find('.returndisplayoverride');
                $(retovd).each(function(){


                    //get the date
                    let rdt = $(this).attr('data-sub-entrydate');
                    let totalme = $(this);
                    let mytotal = 0;
                    proddate = rdt;
             //       console.log('date: ' + proddate);


                    //get all NON-Primary rows for this product
                    let nonprimes = $('#' + tblid).find('tr.siteinvresultrow:not(.isprimarysite)[data-sub-invname="' + product + '"]');
                    $(nonprimes).each(function(){

                        //get all field values for this product on this date
                        let retfields = $(this).find('.pair2[data-sub-entrydate="' + proddate + '"]');
                        $(retfields).each(function(){

                            let tdval = $(this).text();
                            mytotal += (tdval * 1);
                     //       console.log('product: ' + product + ' date: ' + proddate + ' val: ' + tdval);


                        })
                       // $(totalme).attr('data-override-totals',mytotal);
                        $(totalme).text(mytotal);
                        $(totalme).attr('data-bs-original-title','calculated total of subsites');
                        $(totalme).attr('contenteditable',false);
                    //    $(totalme).addClass('passthrufromattr');
                        console.log('MYTOTAL for product: ' + product + ' date: ' + proddate + ' = ' + mytotal);

                    })

                })

            })

        })








    }





    var allowtooltip = true;

    $(function () {
        console.log('AllScripts - $(function() init');

        //TOOLTIPS
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            //    console.log('TOOLTIP DBG: ' + $(tooltipTriggerEl).attr('class'));
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        console.log('tooltips initialized');

        //row click vendor access for operator
        $('table').on('click','.vendortrigger td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let vid = leID.replace('vendor','');
            location.href='index.php?loc=customer-details&id=' + vid + '&t=vendor&pro=1';

        })
        //rowactiontext vendor access for operator
        $('table').on('click','.textvendortrigger',function(evt){

            let leID = this.id;
            let vid = leID.replace('textvendor','');
            location.href='index.php?loc=customer-details&id=' + vid + '&t=vendor&pro=1';

        })



        //row click vendor access for operator
        $('table').on('click','.vendoroperatortriggerdelivery td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let vid = leID.replace('vendor','');
            location.href='index.php?loc=managevendor&id=' + vid + '&t=operator&pro=1&dest=delivery';

        })
        //rowactiontext vendor access for operator
        $('table').on('click','.textvendoroperatortriggerdelivery',function(evt) {


            let leID = this.id;
            let vid = leID.replace('textvendor','');
            location.href='index.php?loc=managevendor&id=' + vid + '&t=operator&pro=1&dest=delivery';

        })

        //row click vendor access for operator
        $('table').on('click','.vendoroperatortriggerreceiving td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let vid = leID.replace('vendor','');
            location.href='index.php?loc=managevendor&id=' + vid + '&t=operator&pro=1&dest=receiving';

        })
        //rowactiontext vendor access for operator
        $('table').on('click','.textvendoroperatortriggerreceiving',function(evt) {


            let leID = this.id;
            let vid = leID.replace('textvendor','');
            location.href='index.php?loc=managevendor&id=' + vid + '&t=operator&pro=1&dest=receiving';

        })




        //row click site entry for operators
        $('table').on('click','.siteoperatortrigger td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let spl = leID.split('vendorid');

            let id = spl[0].replace('site','');
            let vid = spl[1];
            location.href='index.php?loc=site-deliverydata&id=' + id + '&vid=' + vid + '&t=operator&pro=1';

        })
        //rowactiontext site entry for operators
        $('table').on('click','.textsiteoperatortrigger',function(evt){


            let leID = this.id;
            let spl = leID.split('textvendorid');

            let id = spl[0].replace('site','');
            let vid = spl[1];
            location.href='index.php?loc=site-deliverydata&id=' + id + '&vid=' + vid + '&t=operator&pro=1';

        })

        //row click site entry for operators
        $('table').on('click','.siteoperatortriggerreceiving td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let spl = leID.split('vendorid');

            let id = spl[0].replace('site','');
            let vid = spl[1];
            location.href='index.php?loc=site-receivingdata&id=' + id + '&vid=' + vid + '&t=operator&pro=1';

        })
        //rowactiontext site entry for operators
        $('table').on('click','.textsiteoperatortriggerreceiving',function(evt){


            let leID = this.id;
            let spl = leID.split('textvendorid');

            let id = spl[0].replace('site','');
            let vid = spl[1];
            location.href='index.php?loc=site-receivingdata&id=' + id + '&vid=' + vid + '&t=operator&pro=1';

        })




        //row click site entry for vendors
        $('table').on('click','.sitevenoperatortrigger td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let spl = leID.split('vendorid');

            let id = spl[0].replace('site','');
            let vid = spl[1];
            location.href='index.php?loc=site-receivingdata&id=' + id + '&vid=' + vid + '&t=operator&pro=1';

        })
        //rowactiontext site entry for vendors
        $('table').on('click','.textsitevenoperatortrigger',function(evt){


            let leID = this.id;
            let spl = leID.split('textvendorid');

            let id = spl[0].replace('site','');
            let vid = spl[1];
            location.href='index.php?loc=site-receivingdata&id=' + id + '&vid=' + vid + '&t=operator&pro=1';

        })


        //row click site entry for vendors
        $('table').on('click','.siteoperatortriggerdelivery td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let spl = leID.split('vendorid');

            let id = spl[0].replace('site','');
            let vid = spl[1];
            location.href='index.php?loc=site-deliverydata&id=' + id + '&vid=' + vid + '&t=operator&pro=1';

        })
        //rowactiontext site entry for vendors
        $('table').on('click','.textsiteoperatortriggerdelivery',function(evt){


            let leID = this.id;
            let spl = leID.split('textvendorid');

            let id = spl[0].replace('site','');
            let vid = spl[1];
            location.href='index.php?loc=site-deliverydata&id=' + id + '&vid=' + vid + '&t=operator&pro=1';

        })


        $('form').on('change','#state',function(){
            $('.stateholdr').addClass('hasval');
        })


        //row click vendor access for weekly report
        $('table').on('click','.weeklyreporttrigger td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let vid = leID.replace('vendor','');
         // OLD   location.href='index.php?loc=report-weekly&id=' + vid + '&vid=' + vid;
            location.href='index.php?loc=report-monthly&weeks=1&id=' + vid + '&vid=' + vid;

        })
        //rowactiontext vendor access for weekly report
        $('table').on('click','.textweeklyreporttrigger',function(evt){

            let leID = this.id;
            let vid = leID.replace('textvendor','');
         //   location.href='index.php?loc=report-weekly&id=' + vid + '&vid=' + vid;
            location.href='index.php?loc=report-monthly&weeks=1&id=' + vid + '&vid=' + vid;

        })




        //row click vendor access for monthly report
        $('table').on('click','.monthlyreporttrigger td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let vid = leID.replace('vendor','');
            location.href='index.php?loc=report-monthly&id=' + vid + '&vid=' + vid;

        })
        //rowactiontext vendor access for monthly report
        $('table').on('click','.textmonthlyreporttrigger',function(evt){

            let leID = this.id;
            let vid = leID.replace('textvendor','');
            location.href='index.php?loc=report-monthly&id=' + vid + '&vid=' + vid;

        })


        //row click vendor access for sustainability metrics
        $('table').on('click','.totalsreporttrigger td.align-middle',function(evt){

            let triggerrow = $(this).parent();
            let leID = $(triggerrow).attr('id');
            let vid = leID.replace('vendor','');
            location.href='index.php?loc=report-totals&id=' + vid + '&vid=' + vid;

        })
        //rowactiontext vendor access for sustainability metrics
        $('table').on('click','.texttotalsreporttrigger',function(evt){

            let leID = this.id;
            let vid = leID.replace('textvendor','');
            location.href='index.php?loc=report-totals&id=' + vid + '&vid=' + vid;

        })





        $('.isdataentry').on('click','input.form-control',function(evt){

            $(this).select();

        })

        $('.note').on('mousedown','.closr',function(evt){
            console.log('mousedown');
            evt.preventDefault();
            $(this).parent().parent().addClass('hidr');
            allowtooltip = false;
            console.log('hidr added');
            setTimeout(function(){
                allowtooltip = true;
            },100);
        })

        $('.note').on('click','.deldaynote',function(){
            if(isMobile.phone===true) {
                console.log('click');
                $(this).find('.tooltip').removeClass('hidr');
            }
        })


        $('form').on('click','.finalizedata',function(evt){

            let bAllow = false;

            let enabledgrps = $('.groupenabled');
            let activeels = $(enabledgrps).length;
            console.log(activeels);

            let oompletegrps = $('.groupenabled.groupcomplete');
            let oompleteels = $(oompletegrps).length;
            console.log(oompleteels);

            if(activeels - oompleteels < 1 || isforcedupdate===true){
                bAllow = true;
            }


           // if(bAllow===false){
           //     alert("All products must be saved before finalizing.");
         //   }
         //   else {

                let leID = this.id;
                console.log(leID);
                console.log('#finalize' + leID);
                let leform = $('#finalize' + leID);
                console.log(leform);
                $(leform).submit();
           // }

        })


        /* QR CODES */
        $(document).on('click','.vendorqrcode',function(evt){

            //eg: siteid=1-vendorid=4-prod=zipper_bags-count=50
            let leID = this.id;
            let spl = leID.split('-');

            let sidspl = spl[0].split('=');
            let vidspl = spl[1].split('=');
            let prodspl = spl[2].split('=');
            let cntspl = spl[3].split('=');

            let iframeloc='?pg=site-receivingdata&' + spl[0] + '&' + spl[1] + '&' + spl[2] + '&' + spl[3] + '&t=vendor' ;
            modalIframe(iframeloc,'show');

        })
        $(document).on('click','.operatorqrcode',function(evt){

            //eg: siteid=1-vendorid=4-prod=zipper_bags-count=50
            let leID = this.id;
            let spl = leID.split('-');

            let sidspl = spl[0].split('=');
            let vidspl = spl[1].split('=');
            let prodspl = spl[2].split('=');
            let cntspl = spl[3].split('=');

            let iframeloc='?pg=site-deliverydata&' + spl[0] + '&' + spl[1] + '&' + spl[2] + '&' + spl[3] + '&t=operator' ;
            modalIframe(iframeloc,'show');

        })

        $('#aform-inventory').on('click','.createproduct',function(evt){

            //eg: siteid=1-vendorid=4-prod=zipper_bags-count=50

            let productname = prompt('Enter new product name.');

            let bAllow = false;
            if (productname === null) {
                bAllow = false;
            } else {
                bAllow = true;
            }


            if(bAllow){
                location.href = 'index.php?loc=aform-inventory&t=inventory&ng=tracking&np=' + productname;
            }



        })

        $(document).on('click','.site.delete',function(evt){

            if(confirm("This will permanently delete the Site and all associated tracking records.\n\n This action can not be undone.\n\nDelete?")) {
                //eg: siteid=1-vendorid=4-prod=zipper_bags-count=50
                let leID = this.id;
                console.log('site.delete ' + leID);
                let spl = leID.split('vendor');

                console.log('site id str ' + spl[0]);
                let siteID = spl[0].replace('site', '');
                console.log('site id  ' + siteID);
                let venID = spl[1];


                let values = {};
                values['sid'] = siteID;
                values['vid'] = venID;
                let formid = 'deletesite';

                let upvals = JSON.stringify(values);
                console.log("onclick delete site " + upvals);
                execAjax(formid, upvals, '');
            }

        })

        $(document).on('click','.vendor.delete',function(evt){


            if(confirm("This will permanently delete the Vendor, all of its Sites and all associated tracking records.\n\n This action can not be undone.\n\nDelete?")) {

                let leRow = $(this).parent().parent().parent();
                let leID = $(leRow).attr('id');
                console.log('vendor.delete ' + leID);

                let intID = leID.replace('vendor', '');
                console.log('db id ' + intID);

                let values = {};
                values['vid'] = intID;
                let formid = 'deletevendor';

                let upvals = JSON.stringify(values);
                console.log("onclick delete vendor " + upvals);
                execAjax(formid, upvals, '');
            }

        })

        $(document).on('click','.operator.delete',function(evt){


            if(confirm("This action can not be undone.\n\nDelete?")) {

                let leRow = $(this).parent().parent().parent();
                let leID = $(leRow).attr('id');
                console.log('operator.delete ' + leID);

                let intID = leID.replace('operator', '');
                console.log('db id ' + intID);

                let values = {};
                values['oid'] = intID;
                let formid = 'deleteoperator';

                let upvals = JSON.stringify(values);
                console.log("onclick delete operator " + upvals);
                execAjax(formid, upvals, '');
            }

        })

        $('form').on('click','.form-check.form-switch',function(evt){


            let isnondyn = $(this).parent().hasClass('is_primaryholdr');

           // console.log(leID + ' clicked');
            let check = $(this).find('.form-check-input');

            let leID = $(check).attr('id');
            console.log('id: ' + leID);

            let idstr = leID.replace('is_','');

            let container = $(this).parent().parent();
            let numberholdr = $(container).find('.numberholdr.' + idstr + '_amtholdr');
            let quantity = $(numberholdr).find('.form-control');


            let bIsChecked = ($(check).prop( "checked") === true);
          //  console.log(leID + 'checkbox checked = ' + bIsChecked);
            if(bIsChecked===true){
                $(check).attr( "checked",true);

                if(!isnondyn) {
                    $(container).addClass('groupenabled');
                    $(container).removeClass('groupdisabled');

                    $(numberholdr).addClass('jsonform-required');


                    $(quantity).attr('required', true);
                    $(quantity).focus();
                }
                else{
                    $('body').addClass('primarysite');
                }
            }
            else{
                $(check).attr( "checked",false);

                if(!isnondyn) {
                    $(container).removeClass('groupenabled');
                    $(container).addClass('groupdisabled');

                    $(numberholdr).removeClass('jsonform-required');


                    $(quantity).attr('required', false);
                }
                else{
                    $('body').removeClass('primarysite');
                }

            }

        })

        $(document).on('submit','form:not("#recoverEmail")',function(evt){


            let formel = $(this);
            let leID = this.id;

            if(leID==='finalizedelivery' || leID==='finalizereceiving'){
                $('.showsuccessholdr').removeClass('hidr');
            }


            if(leID!='searchform') {
                evt.preventDefault();
                console.log('submission init');

                $(formel).find('.btn-primary').attr('disabled',true);
                $(formel).find('.btn-primary').addClass('btn-disabled');

                dynSubmit(leID, this, '');
                console.log('submission complete');
            }

        })

        $(document).on('click','.bootstrap-growl.alert .close',function(evt){
            console.log('growl closed');
            $(this).parent().addClass('hidr');
        })

        $('form').on('click','#mssearchbox',function(evt){

            console.log('search clicked');
            let elwidth = $(this).width();
            let lefield = $(this);
            let leval = $(this).val();
            let keyCode = evt.keyCode || evt.which;
            //   console.log('keydown phone filter: keycode - ' + keyCode);
            if (keyCode !== 9) {
             /*   evt.preventDefault(); */
                console.log('val=' + $(this).val());
                if($(this).val()!==''){
                    $(this).parent().parent().addClass('hascontent');
                }
                else{
                    $(this).parent().parent().removeClass('hascontent');
                    list.fuzzySearch('');
                   /*
                    setTimeout(function(evt){
                        var kp = $.Event("keypress", { keyCode: 8 });
                        var kd = $.Event("keydown", { keyCode: 8 });
                        var ku = $.Event("keyup", { keyCode: 8 });
                        $('#mssearchbox').val('');
                        $("#mssearchbox").trigger(kp);
                        $("#mssearchbox").trigger(kd);
                        $("#mssearchbox").trigger(ku);
                    },1000);

                    */

                }
            }
        })

        $('body').on('mousedown','.searchclosr',function(evt){

            /*
            console.log('search close clicked');
            var e = $.Event("keyup", { keyCode: 9 });
            $('#mssearchbox').val('');
            $("#mssearchbox").trigger( e );

             */

        })


        $('#showfilters').on('click','.filterpillbutton',function(evt){


            console.log('filterpillbutton dismissed');
            let leID = this.id;

            let lecat = $(this).attr('data-ftype');

            let levalue = leID.replace('filter-button-','');

            console.log('filter dismissed - ' + levalue + ' category: ' + lecat);

            $('input.form-check-input[name="' + levalue + '"]').prop("checked",false);

            $(this).parent().parent().find('#filter-' + levalue).remove();
          //  setTimeout(function(){
                let holdr = $('#' + lecat + 'pillholdr');
                let newhtml = holdr.html();
                if(newhtml.indexOf('<span')>-1){
                    console.log('no content replaced for #' + lecat + 'pillholdr');
                }
                else{
                    /*
                    let cont = (lecat==='type') ? ' T Y P E S' : ' U N I T S';
                    cont = (lecat==='condition') ? ' C O N D I T I O N S'
                    newhtml = newhtml.replace(' class="','<div@class="');
                    newhtml = newhtml.replace('" id="','"@id="');
                    newhtml = newhtml.replaceAll(' ','');
                    newhtml = newhtml.replaceAll("\n",'');
                    newhtml = newhtml.replaceAll("@",' ');
                    console.log('all content replaced #' + lecat + 'pillholdr');
                    $(holdr).html(newhtml);

                     */
                }



                let hidden = $('#searchform').find('#hiddenfield-' + lecat + '-' + levalue);
                if($(hidden).length){
                    $(hidden).remove();
                }

                $('body').removeClass(lecat + '-' + levalue);
          //  },500)



        });

        $('#filtercheckboxes').on('click','.form-check-input',function(evt){



            let leID = this.id;

            let lecat = $(this).attr('data-ftype');

            console.log('filtercheckbox clicked - ' + leID + ' category: ' + lecat);

            let bIsChecked = ($(this).prop( "checked") === true);

            let istrue = (bIsChecked===true) ? 'true' : 'false';

            console.log('istrue-' + istrue);

            let levalue = leID.replace('check-filter-','');


            let showvalue = levalue.replaceAll('-',' ');

            let newpill = '<span id="filter-' + levalue + '" data-ftype="' + lecat + '" class="badge bg-300 text-600 py-0 filterpill">' + capFirstLetter(showvalue) + '<button id="filter-button-' + levalue + '" data-ftype="' + lecat + '" class="btn btn-link btn-sm p-0 text-600 ms-1 filterpillbutton"><span class="fas fa-times fs--2"></span></button></span>';


            let newhiddenfield = '<input type="hidden" class="hiddensearchfield" name="' + lecat + '-' + levalue + '" value="1" id="hiddenfield-' + lecat + '-' + levalue + '">';

            let filterpills = $('#showfilters');
          //  let searchform = $()





            if(bIsChecked===true){
                $(filterpills).find('#' + lecat + 'pillholdr').append(newpill);
                $('#searchform').append(newhiddenfield);
                $('body').addClass(lecat + '-' + levalue);
            }
            else{

                $(filterpills).find('span#filter-' + levalue).remove();
              /*  let holdr = $('#' + lecat + 'pillholdr');
                let newhtml = holdr.html().replaceAll(' ','').replaceAll("\n",'');
                $(holdr).html(newhtml); */

                let hidden = $('#searchform').find('#hiddenfield-' + lecat + '-' + levalue);
                if($(hidden).length){
                    $(hidden).remove();
                }
                $('body').removeClass(lecat + '-' + levalue);
            }





        });

        $('.navbar').on('click','.nvm',function(evt){


            console.log('navbar toggle on');
            let leID = this.id;
            let otherlink = (leID==='navlinkmarket') ? 'navlinkmedia' : 'navlinkmarket';

            console.log('thislink - ' + leID + ' otherlink - ' + otherlink);
            let otherel = $('#' + otherlink);
            let thisel = $('#' + leID);


            $(thisel).addClass('on');
            $(otherel).removeClass('on');




        });

        $('#aform-signup').on('blur','.form-control,.form-select',function(evt){
            $(this).parent().addClass('was-validated');
        });


        $('#aform-post').on('blur','.form-control,.form-select',function(evt){
            $(this).parent().addClass('was-validated');
        });

        $('#aform-post').on('click','.embedded',function(evt){
            $(this).parent().parent().parent().addClass('was-validated').addClass('iscomplete');
        });

        $('#aform-post').on('click','.form-check-input.normal',function(evt){
            $(this).parent().parent().addClass('was-validated');
        });

        $('#aform-createsite').on('keyup click','.sitequantity',function(evt){
         //   $(this).parent().parent().addClass('was-validated');
            let leID = this.id;
            let allotmentid = leID.replace('_amt','allott');

            let numbercont = $(this).parent();

            let valu = $(this).val();
            let valu_allott = $('#' + allotmentid).val();

            console.log(valu + ' - ' + valu_allott);

            if(valu * 1 > valu_allott * 1){
                $(numbercont).addClass('overallotted');
            }
            else{
                $(numbercont).removeClass('overallotted');
            }


        });


        $('form').on('keyup','#mssearchbox',function(evt){

        //    console.log('key pressed');
            let lefield = $(this);
            let leval = $(this).val();
            let keyCode = evt.keyCode || evt.which;
            //   console.log('keydown phone filter: keycode - ' + keyCode);
            if (keyCode !== 9) {
             /*   evt.preventDefault(); */
                console.log('val=' + $(this).val() + ' keycode=' + keyCode);
                if($(this).val()!==''){

                    $(this).parent().parent().addClass('hascontent');
                }
                else{
                  //  $(this).keyup();
                    $(this).parent().parent().removeClass('hascontent');
                }
            }
        })

        $('form').on('keyup','#itemfeevalue',function(evt){

            //    console.log('key pressed');
           // let lefield = $(this);
            let leval = $(this).val();
            console.log(leval);
            let tax = '';
            let fivepercentfee = '';
            let onlinefee = '';
            if(leval==='$0.00'||leval===''){

            }
            else{
                let taxstr = leval.replace('$','');
                console.log(taxstr);
                tax = taxstr * .08;
                fivepercentfee = taxstr * .05;
                onlinefee = taxstr * .03;
                console.log(tax);
            }

            $('#taxvalue').val(tax);
            $('#feevalue').val(fivepercentfee);
            $('#payvalue').val(onlinefee);


        })

        $('textarea').on('input', function () {
            this.style.height = 'auto';

            this.style.height =
                (this.scrollHeight) + 'px';
        });


        $('.reporttable:not(.totalstable) .siteinvresultrow').on('input','.pair1,.returned,.damaged',function(evt){


            console.log('contenteditable input change');

            let modenode = $(this).closest('.table-responsive.scrollbar');
            let isPrimaryMode = $(modenode).hasClass('primarymode');
            console.log('isPrimaryMode: ' + isPrimaryMode);



			if($(this).hasClass('damaged')==false) {
                updateReportDBVal(this);
            }
            calcBodyMainTotals(this,isPrimaryMode);


            if(isPrimaryMode){
                calcDailyPrimaryReturnVals();
            }


            console.log($(this) + "\n" + ' classlist: ' + $(this).classList);


        })


        $('.reporttable.totalstable').on('input','.balance.damaged',function(){
            updateReportDBVal(this);
            calcTotalsTable();
        })



        $('table.reporttable .siteinvresultrow:not(.totalsrow)').on('click','.typecol',function(evt){

            let par = $(this).parent();
            let prod = $(par).attr('data-sub-invname');


            let dispprodname = $(par).attr('data-product');



            let tottarrow = $('table.totalstable .siteinvresultrow[data-sub-invname="' + prod + '"]:not(.totalsrow)');


            let titlerows = $('table.reporttable .siteinvresultrow[data-sub-invname="' + prod + '"]:not(.totalsrow)');


            let isactivated = $(tottarrow).hasClass('showsingle');



            console.log('prod toggle:' + prod);
            console.log('is active:' + isactivated);
            let titleshow = "Click to show all products";
            if(isactivated) {
                $('table:not(.totalstable) .siteinvresultrow:not([data-sub-invname="' + prod + '"])').removeClass('hidr');
                $(tottarrow).removeClass('showsingle');
                $('table.reporttable').removeClass('showsinglemode');
                titleshow = "Click to show only " + dispprodname;
            }
            else{
                $('table:not(.totalstable) .siteinvresultrow:not([data-sub-invname="' + prod + '"])').addClass('hidr');
                $(tottarrow).addClass('showsingle');
                $('table.reporttable').addClass('showsinglemode');

            }

     //       $(titlerows).find('.typecol').attr('title',titleshow);
            $(titlerows).find('.typecol').attr('data-bs-original-title',titleshow);

    //        $(this).attr('title',titleshow);
    //        $(this).attr('data-bs-original-title',titleshow);


        })







        console.log('AllScripts - $(function() complete');
    })

    /* ------------ REMEMBER ME ------------- */
    var remembermepath = '<?php echo $USR->rtprot; ?><?php echo $USR->rturl; ?>/index.php?loc=login';
    function getcookiedata() {
        var user = getCookie('email');
        //   var pswd = getCookie('password');
        var remember = getCookie('rememberme');

        console.log('getcookiedata user:' + user + ' remember: ' + remember);

        document.getElementById('email').value = user;
        //  document.getElementById('password').value = pswd;
        if(remember == 'yes') {
            document.getElementById('rememberme').checked = true;
        } else {
            document.getElementById('rememberme').checked = false;
        }
    }
    function getCookie(cname) {
        console.log('getCookie ' + cname);
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while(c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if(c.indexOf(name) == 0) {

                let cook = c.substring(name.length, c.length);
                console.log('getCookie cname:' + cname + ' value: ' + cook);
                return cook;
            }
        }
        return "";
    }
    /* ------------ END REMEMBER ME ------------- */

    /**
     * SESSION NEVER DIES
     */
    var globzombies = <?php echo (isset($_SESSION['countzombielives'])) ? $_SESSION['countzombielives'] : 0 ; ?>;
    var sessionextender;
    var pulsetime = 10000;
    function checkZombies(){

        $.get('_ensure_zombie_session.php?&zombiecount=' + globzombies,function(response) {
            var data = jQuery.parseJSON(response);


            globzombies = data['zombiecount'];
          //  console.log(data);
            // console.log(data.navcollapsestate);
            //console.log('' + data.zombiecount + ' countzombielives:' + data.countzombielives + ' nav:' + data.navcollapsestate);
        })

    }
    function sessionNeverDie(overridetime){

        sessionextender = setInterval(function(){

            checkZombies();


        },overridetime);
        //if called explicitly to save state - reset the auto timer after the explicit call is complete.
        if(overridetime<pulsetime){
            let resetTmr = setInterval(function(){
              //  clearInterval(sessionextender);
            //    sessionNeverDie(pulsetime);
            },overridetime);
        }
    }




    $('document').ready(function(){
        console.log('document ready init ');

        //rememberme cookie management
        $('#rememberme').click(function () {
            console.log('rememberme clicked');
            if ($('#rememberme').is(':checked')) {

                let rempath = "rememberme=yes;path=" + remembermepath;
                document.cookie = rempath;
                console.log('rememberme set remember to: ' + rempath);

                var u = document.getElementById('email').value;
                // var p = document.getElementById('password').value;
               // var r = document.getElementById('rememberme').value;

                let fullpath = "email=" + u + ";path=" + remembermepath;
                document.cookie = fullpath;
                console.log('rememberme set cookie to: ' + fullpath);
                //   document.cookie = "password=" + p + ";path=" + remembermepath;
            }
            else {
                document.cookie = "rememberme=no;path=" + remembermepath;
                document.cookie = "email=;path=" + remembermepath;
                console.log('rememberme set cookies to empty');

                //   document.cookie = "password=;path=" + remembermepath;

            }
        });


        //login cookie for saving email
        if(<?php echo ($currentpage==='login') ? 1 : 0; ?>) {
            console.log('check rememberme cookies');
            let getCookieOn = getCookie('rememberme');
            if (getCookieOn === 'yes') {
                let getEmailCookie = getCookie('email');
                if (getEmailCookie !== '') {
                    $('#email').val(getEmailCookie);
                    $('#rememberme').click();
                }
            }
        }

        /*
        $(window).resize(function() {
            wrapperWidth = $wrapper.width();//re-get the width
            $wrapper.text(wrapperWidth);//update the text value
        });

         */

        //mobile phone colsole log
        if(isMobile.phone===true){
                console.log('Mobile Phone Detacted.');
        }
        else{
            console.log('Desktop Detacted.');
        }

        <?php
        //create site
        if(isset($currentpage) && $currentpage==='aform-createsite'){
        ?>


                $.each($('.sitequantity'), function(i, field) {


                    let fname = field.name;
                    let fval = field.value;
                    let totsid = fname.replace('_amt','tots');
                    let numbercont = $('#' + fname).parent();

                    let valu = fval;
                    let valu_tot = $('#' + totsid).val();

                    if(valu * 1 > valu_tot * 1){
                        $(numbercont).addClass('overallotted');
                    }
                    else{
                        $(numbercont).removeClass('overallotted');
                    }

              //      values[field.name] = field.value;
                    console.log(valu + ' = ' + valu_tot);
                });

                let prim = $('#is_primary');
                let bIsChecked = ($(prim).prop( "checked") === true);

                if(bIsChecked){
                    $('body').addClass('primarysite');
                }

        <?php
    }

        $passid = req('id','n');
        $weekstart = req('rweekstart','s');
       // $passvid = req('vid','n');

		//reporting init
        if((isset($currentpage) && $currentpage==='report-monthly') && $passid>0 && $weekstart!=''){
        ?>
                let triggercell = $('#table0 > tbody > tr.siteinvresultrow.firstrow:first-of-type > td:nth-of-type(3)');


                primarysite = $(triggercell).closest('.siteinvresultrow').attr('data-sitename');

                let modenode = $(triggercell).closest('.table-responsive.scrollbar');
                let isPrimaryMode = $(modenode).hasClass('primarymode');
                primarymode = isPrimaryMode;

                console.log('isPrimaryMode: ' + isPrimaryMode + ' primarysite:' + primarysite);

                if(isPrimaryMode==false){
                    calcBodyMainTotals(triggercell,isPrimaryMode);
                }
                else{

                    calcDailyPrimaryReturnVals();

                }
                $('.reporttable.totalstable').removeClass('invis');

               <?php
        }

        //post init
        if(isset($currentpage) && $currentpage==='aform-post'){

                   $dtm = new DateTime();
                   $dtm->modify("+ 1 day");
                   $tomorrow = $dtm->format('Y-m-d');
                   $dtm->modify("+ 1 month");
                   $nextmonth = $dtm->format('Y-m-d');
               ?>
            const fp = flatpickr('#expiration_d',{
                enable: [
                    {
                        from: "<?php echo $tomorrow; ?>",
                        to: "<?php echo $nextmonth; ?>"
                    }
                ]
            });
            <?php
            $pid = req('id','n');
                if($pid>0){
                ?>


            <?php
            }
        }
        ?>

        if(uripars.loc==='login'){
            if (isMobile.phone) {
                console.log('phone found');

                $('#ismobile').val('1');

                /*
                let atars = $('a');
                console.log(atars);
                console.log('links found: ' + $(atars).length);
                let ainc = 0;
                $(atars).each(function (index,value) {
                    let href = $(this).attr('href');
                    if (href != "#" && href.indexOf('stc=') < 0) {
                        if (href.indexOf('?') < 0) {
                            href += '?stc=' + uripars.stc;
                        }
                        else {
                            href += '&stc=' + uripars.stc;
                        }
                        $(this).attr('href',href);
                    }
                    else { console.log('item ' + ainc + ' skipped.  href' + href); }
                    ainc++;
                })
                console.log('links checked: ' + ainc);

                let ftars = $('form');
                console.log(ftars);
                console.log('forms found: ' + $(ftars).length);
                let finc = 0;
                $(ftars).each(function (index,value) {
                    let action = $(this).attr('action');
                    if (action != "" && action.indexOf('stc=')<0) {
                        if (action.indexOf('?') < 0) {
                            action += '?stc=' + uripars.stc;
                        }
                        else {
                            action += '&stc=' + uripars.stc;
                        }
                        $(this).attr('action', action);
                    }
                    else { console.log('item ' + ainc + ' skipped.  action:' + action); }
                    finc++;

                })
                console.log('forms checked: ' + finc);

*/

            }
            else {
                console.log('no phone found');
                $('#ismobile').val('0');
            }
        }








        sessionNeverDie(pulsetime,false);
        console.log('session mgmt init');

        console.log('AllScripts - document ready complete ');

    });




</script>

<script>
    console.log('AllScripts EOP');
</script>
