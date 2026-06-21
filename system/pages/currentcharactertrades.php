<?php
/**
 *
 * Char Bazaar
 *
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Current Auctions';

if ($logged) {
    $getAccountCoins = $db->query("SELECT `id`, `premdays`, `coins`, `coins_transferable` FROM `accounts` WHERE `id` = {$account_logged->getId()}");
    $getAccountCoins = $getAccountCoins->fetch();
    } else {
        $account_logged = null;
    }

// GET PAGES
$subtopic = $_GET['subtopic'] ?? null;
$getPageDetails = $_GET['details'] ?? null;
$getPageAction = $_GET['action'] ?? null;
// GET PAGES

/* CHAR BAZAAR CONFIG */
$charbazaar_tax = $config['bazaar_tax'];
$charbazaar_bid = $config['bazaar_bid'];
$charbazaar_create = $config['bazaar_create'];
/* CHAR BAZAAR CONFIG END */

/* COUNTER CONFIG */
$showCounter = true;
/* COUNTER CONFIG END */

// // =====================================================
// // EXECUTA O PROCESSAMENTO SEMIAUTOMÁTICO DO BAZAAR
require SYSTEM . 'pages/char_bazaar/semifinishauction.php';
// // =====================================================

?>

<?php 
//ACCOUNT COINS TOP BOX
if ($logged) {
    require SYSTEM . 'pages/char_bazaar/coins_balance.php';
} 
//ACCOUNT COINS TOP BOX
?>

<?php
//FIRST PAGE - SHOW AUCTIONS
if (!$getPageDetails) {
    if (!$logged) {
        ?>
            <div class="SmallBox">
                <div class="MessageContainer">
                    <div class="BoxFrameHorizontal"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeLeftTop"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeRightTop"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></div>
                    <div class="Message">
                        <div class="BoxFrameVerticalLeft"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></div>
                        <div class="BoxFrameVerticalRight"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></div>
                        <table style="width: 100%;">
                            <tbody>
                            <tr>
                                <td>
                                    <div style="float: right;">
                                        <a href="?account/manage" target="_self" rel="noreferrer">
                                            <div class="BigButton"
                                                style="background-image:url(<?= $template_path; ?>/images/global/buttons/sbutton.gif)">
                                                <div onmouseover="MouseOverBigButton(this);"
                                                    onmouseout="MouseOutBigButton(this);">
                                                    <div class="BigButtonOver"
                                                        style="background-image: url(<?= $template_path; ?>/images/global/buttons/sbutton_over.gif); visibility: hidden;"></div>
                                                    <input name="auction_confirm" class="BigButtonText" type="button"
                                                        value="Login"></div>
                                            </div>
                                        </a>
                                    </div>
                                    <p><b>Use Tibia's character auction feature to sell or purchase Tibia characters without
                                            risk!</b></p>
                                    <p><b>Log in</b> to submit a bid to <b>purchase a Tibia character</b> from another
                                        player for your Tibia account!</p>
                                    <p>To <b>sell a Tibia character</b> from your account to another player, log into the
                                        <b>Tibia Client</b> and set up an auction.</p>
                                    <p>Note that Tibia characters can only be <b>purchased with transferable Tibia
                                            Coins!</b></p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="BoxFrameHorizontal"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeRightBottom"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeLeftBottom"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></div>
                </div>
            </div>
            <br>
            <?php } else {
                        ?>
            <div class="SmallBox">
                <div class="MessageContainer">
                    <div class="BoxFrameHorizontal"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeLeftTop"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeRightTop"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></div>
                    <div class="Message">
                        <div class="BoxFrameVerticalLeft"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></div>
                        <div class="BoxFrameVerticalRight"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></div>
                        <table style="width: 100%;">
                            <tbody>
                            <tr>
                                <td>
                                    <p><b>Use Tibia's character auction feature to sell or purchase Tibia characters without
                                            risk!</b></p>
                                    <p>To <b>sell a Tibia character</b> from your account to another player, log into the
                                        <b>Tibia Client</b> and set up an auction.</p>
                                    <p>Note that Tibia characters can only be <b>purchased with transferable Tibia
                                            Coins!</b></p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="BoxFrameHorizontal"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeRightBottom"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeLeftBottom"
                        style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></div>
                </div>
            </div>
            <br>
            <?php
            } 
            ?>

            <div class="TableContainer">
                <div class="CaptionContainer">
                    <div class="CaptionInnerContainer">
                        <span class="CaptionEdgeLeftTop"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightTop"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                        <span class="CaptionBorderTop"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/table-headline-border.gif);"></span>
                        <span class="CaptionVerticalLeft"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></span>
                        <div class="Text">Current Auctions</div>
                        <span class="CaptionVerticalRight"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></span>
                        <span class="CaptionBorderBottom"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/table-headline-border.gif);"></span>
                        <span class="CaptionEdgeLeftBottom"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightBottom"
                            style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                    </div>
                </div>
                <table class="Table3" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td>
                            <div class="InnerTableContainer">
                                <table style="width:100%;">
                                    <tbody>
                                    <?php if ($getPageAction != 'bid') {
                                    $subtopic = 'currentcharactertrades';
                                    $dateLimit = date('Y-m-d H:i:s');
                                    $auctions = $db->query("SELECT `id`, `account_old`, `account_new`, `player_id`, `price`, `date_end`, `date_start`, `bid_account`, `bid_price`, `status` FROM `myaac_charbazaar` WHERE `date_end` >= '{$dateLimit}' ORDER BY `date_start` DESC");
                                    //Exibe alerta se não houver nenhuma auction existente no momento
                                    if ($auctions->rowCount() == 0) {
                                            echo <<<HTML
                                            <tr>
                                                <td colspan="100%" style="text-align: center; padding: 20px;">
                                                    <p style="color: #b32d2d; font-weight: bold; margin: 0;">
                                                        There are no auctions yet, but how about creating your own? 
                                                        <a href="?createcharacterauction">it's simple!</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            HTML;
                                        } else {
                                            require SYSTEM . 'pages/char_bazaar/list_auctions.php';
                                        }
                                    } else {
                                    //Exibe apenas a auction com o personagem alvo da bid
                                    $subtopic = 'currentcharactertrades';
                                    $dateLimit = date('Y-m-d H:i:s');
                                    $auctions = $db->query("SELECT `id`, `account_old`, `account_new`, `player_id`, `price`, `date_end`, `date_start`, `bid_account`, `bid_price`, `status` FROM `myaac_charbazaar` WHERE `id` = " . (int)$_POST['auction_iden']);
                                    $auctions = $auctions->fetchAll(); //Garante que continuará sendo array
                                    require SYSTEM . 'pages/char_bazaar/list_auctions.php';
                                    
                                    }?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
    <?php } 
    //FIRST PAGE - SHOW AUCTIONS END
    ?>



<?php
// AUCTION DETAILS -->
if ($getPageDetails) {
    require SYSTEM . 'pages/char_bazaar/details.php';
} 
//AUCTION DETAILS END
?>


<?php
if ($getPageAction == 'bid') {

    $Auction_iden = $_POST['auction_iden'];
    $Auction_maxbid = $_POST['maxbid'];

    /* GET INFO CHARACTER */
    $getAuction = $db->query('SELECT `id`, `account_old`, `account_new`, `player_id`, `price`, `date_end`, `date_start`, `bid_account`, `bid_price` FROM `myaac_charbazaar` WHERE `id` = ' . $db->quote($Auction_iden) . '');
    $getAuction = $getAuction->fetch();
    /* GET INFO CHARACTER END */


    if ($logged && $getAuction['account_old'] != $account_logged->getId()) {


        /* GET INFO CHARACTER */
        $getCharacter = $db->query('SELECT `name`, `vocation`, `level`, `sex` FROM `players` WHERE `id` = ' . $getAuction['player_id'] . '');
        $character = $getCharacter->fetch();
        /* GET INFO CHARACTER END */

        if ($logged) {
            $getAccount = $db->query("SELECT `id`, `premdays`, `coins`, `coins_transferable` FROM `accounts` WHERE `id` = {$account_logged->getId()}");
            $getAccount = $getAccount->fetch();
        }

        /* CONVERT SEX */
        $character_sex = $config['genders'][$character['sex']];
        /* CONVERT SEX END */

        /* CONVERT VOCATION */
        $character_voc = $config['vocations'][$character['vocation']];
        /* CONVERT VOCATION END */

        if ($Auction_maxbid >= $getAccount['coins_transferable']) {
            $Verif_CoinsAcc = 'false';
        } else {
            $Verif_CoinsAcc = 'true';
        }

        if ($Auction_maxbid > $getAuction['price'] && $Auction_maxbid > $getAuction['bid_price']) {
            $Verif_Price = 'true';
        } else {
            $Verif_Price = 'false';
        }
        ?>
        <div class="TableContainer">
            <div class="CaptionContainer">
                <div class="CaptionInnerContainer">
                    <span class="CaptionEdgeLeftTop"
                          style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                    <span class="CaptionEdgeRightTop"
                          style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                    <span class="CaptionBorderTop"
                          style="background-image:url(<?= $template_path; ?>/images/global/content/table-headline-border.gif);"></span>
                    <span class="CaptionVerticalLeft"
                          style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></span>
                    <div class="Text">You account</div>
                    <span class="CaptionVerticalRight"
                          style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></span>
                    <span class="CaptionBorderBottom"
                          style="background-image:url(<?= $template_path; ?>/images/global/content/table-headline-border.gif);"></span>
                    <span class="CaptionEdgeLeftBottom"
                          style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                    <span class="CaptionEdgeRightBottom"
                          style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                </div>
            </div>
            <table class="Table5" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <div class="InnerTableContainer">
                            <table style="width:100%;">
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="TableContentContainer">
                                            <table class="TableContent" style="border:1px solid #faf0d7;" width="100%">
                                                <tbody>
                                                <tr>
                                                    <td style="font-weight:normal;"><?= $getAccount['coins'] ?>
                                                        <img
                                                            src="<?= $template_path; ?>/images/account/icon-tibiacoin.png">
                                                        (<?= $getAccount['coins_transferable'] ?> <img
                                                            src="<?= $template_path; ?>/images/account/icon-tibiacointrusted.png">)
                                                    </td>
                                                    <td style="font-weight:normal;"><?= $charbazaar_bid ?> <img
                                                            src="<?= $template_path; ?>/images/account/icon-tibiacointrusted.png">
                                                        to give an bid.
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        <div class="CharacterDetailsBlock">
            <div class="TableContainer">
                <div class="CaptionContainer">
                    <div class="CaptionInnerContainer">
                        <span class="CaptionEdgeLeftTop"
                              style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightTop"
                              style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                        <span class="CaptionBorderTop"
                              style="background-image:url(<?= $template_path; ?>/images/global/content/table-headline-border.gif);"></span>
                        <span class="CaptionVerticalLeft"
                              style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></span>
                        <div class="Text">Confirm Bid For Auction</div>
                        <span class="CaptionVerticalRight"
                              style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-vertical.gif);"></span>
                        <span class="CaptionBorderBottom"
                              style="background-image:url(<?= $template_path; ?>/images/global/content/table-headline-border.gif);"></span>
                        <span class="CaptionEdgeLeftBottom"
                              style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightBottom"
                              style="background-image:url(<?= $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
                    </div>
                </div>
                <table class="Table1" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td>
                            <div class="InnerTableContainer">
                                <table style="width:100%;">
                                    <tbody>
                                    <tr>
                                        <td><br>Do you really want to bid the following amount for the character listed
                                            below:
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <br>
                                            <table style="width:50%;">
                                                <tbody>
                                                <?php
                                                if ($Verif_Price == 'true' and $Verif_CoinsAcc == 'true') {
                                                    ?>
                                                    <tr>
                                                        <td style="font-weight: bold;">Your Bid:</td>
                                                        <td><?= $Auction_maxbid ?> <img
                                                                src="<?= $template_path; ?>/images/account/icon-tibiacointrusted.png"
                                                                class="VSCCoinImages" title="Transferable Tibia Coins">
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td style="font-weight: bold; color: red;">Your Bid:</td>
                                                        <td style="font-weight: bold; color: red;"><?= $Auction_maxbid ?>
                                                            <img
                                                                src="<?= $template_path; ?>/images/account/icon-tibiacointrusted.png"
                                                                class="VSCCoinImages" title="Transferable Tibia Coins">
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td style="font-weight: bold;">Character:</td>
                                                    <td><?= $character['name'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: bold;">Level:</td>
                                                    <td><?= $character['level'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: bold;">Profession:</td>
                                                    <td><?= $character_voc ?></td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td><br>If you confirm this bid, a <b>non-refundable deposit</b> of <b><?= $charbazaar_bid ?></b> <img
                                                src="<?= $template_path; ?>/images//account/icon-tibiacointrusted.png"
                                                class="VSCCoinImages" title="Transferable Tibia Coins"> transferable
                                            Tibia Coins will be subtracted from your account's Tibia Coins balance.
                                            <br>
                                            If you  <b>have already placed a bid or someone has placed a higher bid than you</b>, your <b>last bid</b> will be returned to your account.
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <?php
        if ($Verif_Price == 'false') {
            ?>
            <div class="TableContainer">
                <div class="CaptionContainer">
                    <div class="CaptionInnerContainer">
                        <span class="CaptionEdgeLeftTop"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightTop"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-edge.gif);"></span>
                        <span class="CaptionBorderTop"
                              style="background-image:url(<?= $template_path; ?>/images/content/table-headline-border.gif);"></span>
                        <span class="CaptionVerticalLeft"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-vertical.gif);"></span>
                        <div class="Text">Erro</div>
                        <span class="CaptionVerticalRight"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-vertical.gif);"></span>
                        <span class="CaptionBorderBottom"
                              style="background-image:url(<?= $template_path; ?>/images/content/table-headline-border.gif);"></span>
                        <span class="CaptionEdgeLeftBottom"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightBottom"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-edge.gif);"></span>
                    </div>
                </div>
                <table class="Table1" cellspacing="0" cellpadding="0">

                    <tbody>
                    <tr>
                        <td>
                            <div class="InnerTableContainer">
                                <table style="width:100%;">
                                    <tbody>
                                    <tr>
                                        <td>Your bid is lower than the last one.</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <br>
        <?php } ?>
        <?php
        if ($Verif_CoinsAcc == 'false') {
            ?>
            <div class="TableContainer">
                <div class="CaptionContainer">
                    <div class="CaptionInnerContainer">
                        <span class="CaptionEdgeLeftTop"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightTop"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-edge.gif);"></span>
                        <span class="CaptionBorderTop"
                              style="background-image:url(<?= $template_path; ?>/images/content/table-headline-border.gif);"></span>
                        <span class="CaptionVerticalLeft"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-vertical.gif);"></span>
                        <div class="Text">Erro</div>
                        <span class="CaptionVerticalRight"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-vertical.gif);"></span>
                        <span class="CaptionBorderBottom"
                              style="background-image:url(<?= $template_path; ?>/images/content/table-headline-border.gif);"></span>
                        <span class="CaptionEdgeLeftBottom"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightBottom"
                              style="background-image:url(<?= $template_path; ?>/images/content/box-frame-edge.gif);"></span>
                    </div>
                </div>
                <table class="Table1" cellspacing="0" cellpadding="0">

                    <tbody>
                    <tr>
                        <td>
                            <div class="InnerTableContainer">
                                <table style="width:100%;">
                                    <tbody>
                                    <tr>
                                        <td>You don't have enough coins to bid.</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <br>
        <?php } ?>


        <div style="width: 100%; text-align: center; display: flex; justify-content: center;">
            <?php
            if ($Verif_Price == 'true' && $Verif_CoinsAcc == 'true') {
                ?>
                <script>
                    var myModalFinishBid = document.getElementById('ModalOpenFinishBid')
                    var myInputFinishBid = document.getElementById('ModalInputFinishBid')
                    myModalFinishBid.addEventListener('shown.bs.modal', function () {
                        myInputFinishBid.focus()
                    })
                </script>
                <form method="post" action="?subtopic=currentcharactertrades&action=bidfinish">
                <input type="hidden" name="bid_iden" value="<?= $getAuction['id'] ?>">
                <input type="hidden" name="bid_max" value="<?= $Auction_maxbid ?>">
                <div class="BigButton"
                     style="background-image:url(<?= $template_path; ?>/images/global/buttons/sbutton_green.gif)">
                    <div onmouseover="MouseOverBigButton(this);" onmouseout="MouseOutBigButton(this);">
                        <div class="BigButtonOver"
                             style="background-image: url(<?= $template_path; ?>/images/global/buttons/sbutton_green_over.gif); visibility: hidden;">
                            </div>
                        <input name="bid_confirm" class="BigButtonText" type="submit" value="Submit Bid"
                               data-bs-toggle="modal" data-bs-target="#ModalOpenFinishBid"></div>
                </div>
                </form>
                <div class="modal fade" id="ModalOpenFinishBid" data-bs-backdrop="static" data-bs-keyboard="false"
                     tabindex="-1" aria-labelledby="ModalOpenFinishBidLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
        <h5 class="modal-title" id="ModalOpenFinishBidLabel">You bid created!</h5>
		<img src="<?= $template_path; ?>/images/content/circle-symbol-minus.gif" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
      </div>
                            <div class="modal-body">
                                <div style="width: 100%; display: flex; justify-content: center; align-items: center;">
                                    <img src="<?= $template_path; ?>/images/charactertrade/confirm.gif"> <span
                                        style="font-weight: bold; font-size: 16px; padding-left: 10px; text-align: left; color: #ffffff">You submitted a bid successfully.<br><small
                                            style="font-weight: 100;">You will be redirected in a few moments.</small></span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                    <div class="BigButton"
                                         style="background-image:url(<?= $template_path; ?>/images/global/buttons/sbutton_green.gif)">
                                        <div onmouseover="MouseOverBigButton(this);"
                                             onmouseout="MouseOutBigButton(this);">
                                            <div class="BigButtonOver"
                                                 style="background-image: url(<?= $template_path; ?>/images/global/buttons/sbutton_green_over.gif);visibility: hidden;"></div>
                                            <input name="bid_confirm" class="BigButtonText" type="submit" value="Exit">
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <a href="?subtopic=currentcharactertrades" style="margin-left: 3px">
                <div class="BigButton"
                     style="background-image:url(<?= $template_path; ?>/images/global/buttons/sbutton_red.gif)">
                    <div onmouseover="MouseOverBigButton(this);" onmouseout="MouseOutBigButton(this);">
                        <div class="BigButtonOver"
                             style="background-image: url(<?= $template_path; ?>/images/global/buttons/sbutton_red_over.gif); visibility: hidden;"></div>
                        <input class="BigButtonText" type="button" value="Cancel"></div>
                </div>
            </a>
        </div>

        <?php
    }
}
?>


<?php
//REGISTRO NA DB
if ($getPageAction == 'bidfinish') {

    if (isset($_POST['bid_confirm']) && $_POST['bid_max'] && $logged) {
        $idLogged = $account_logged->getId();
        
        // VALIDAÇÃO: Conversão para inteiros para evitar manipulação
        $bid_iden = (int) $_POST['bid_iden'];
        $bid_max = (int) $_POST['bid_max'];

        // VALIDAÇÃO: Verifica se valores são positivos
        if ($bid_iden <= 0 || $bid_max <= 0) {
            echo <<<HTML
            <div class="SmallBox">
                <div class="MessageContainer">
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeLeftTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeRightTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="Message">
                    <div class="BoxFrameVerticalLeft" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <div class="BoxFrameVerticalRight" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <table class="HintBox">
                        <tbody>
                        <tr>
                        <td>
                            <p style="color: #b32d2d; font-weight: bold; text-align: center; margin: 0;">
                            Dados inválidos fornecidos.
                            </p>
                        </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeRightBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeLeftBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                </div>
            </div>
            <br>
            HTML;
            return;
        }

        // Dados da Auction
        $getAuction = $db->query("SELECT `id`, `account_old`, `account_new`, `player_id`, `price`, `date_end`, `date_start`, `bid_account`, `bid_price` FROM `myaac_charbazaar` WHERE `id` = {$db->quote($bid_iden)}");
        
        // VALIDAÇÃO: Verifica se o leilão existe
        if ($getAuction->rowCount() == 0) {
            echo <<<HTML
            <div class="SmallBox">
                <div class="MessageContainer">
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeLeftTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeRightTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="Message">
                    <div class="BoxFrameVerticalLeft" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <div class="BoxFrameVerticalRight" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <table class="HintBox">
                        <tbody>
                        <tr>
                        <td>
                            <p style="color: #b32d2d; font-weight: bold; text-align: center; margin: 0;">
                            Leilão não encontrado.
                            </p>
                        </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeRightBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeLeftBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                </div>
            </div>
            <br>
            HTML;
            return;
        }
        
        $getAuction = $getAuction->fetch();

        // VALIDAÇÃO: Verifica se usuário NÃO é o dono do leilão
        if ($idLogged == $getAuction['account_old']) {
            echo <<<HTML
            <div class="SmallBox">
                <div class="MessageContainer">
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeLeftTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeRightTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="Message">
                    <div class="BoxFrameVerticalLeft" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <div class="BoxFrameVerticalRight" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <table class="HintBox">
                        <tbody>
                        <tr>
                        <td>
                            <p style="color: #b32d2d; font-weight: bold; text-align: center; margin: 0;">
                            Você não pode dar bid no seu próprio leilão.
                            </p>
                        </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeRightBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeLeftBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                </div>
            </div>
            <br>
            HTML;
            return;
        }

        // Dados da bid referente à Auction
        $getAuctionBid = $db->query("SELECT `id`, `account_id`, `auction_id`, `bid`, `date` FROM `myaac_charbazaar_bid` WHERE `auction_id` = {$db->quote($bid_iden)} ORDER BY `bid` DESC LIMIT 1");
        $countAuctionBid = $getAuctionBid->rowCount();
        $getAuctionBid = $getAuctionBid->fetch();

        // VALIDAÇÃO: Verifica se o bid é maior que o atual
        $minimumBid = ($countAuctionBid > 0) ? $getAuctionBid['bid'] + 1 : $getAuction['price'];
        if ($bid_max < $minimumBid) {
            echo <<<HTML
            <div class="SmallBox">
                <div class="MessageContainer">
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeLeftTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeRightTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="Message">
                    <div class="BoxFrameVerticalLeft" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <div class="BoxFrameVerticalRight" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <table class="HintBox">
                        <tbody>
                        <tr>
                        <td>
                            <p style="color: #b32d2d; font-weight: bold; text-align: center; margin: 0;">
                            Seu bid deve ser no mínimo {$minimumBid} coins.
                            </p>
                        </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeRightBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeLeftBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                </div>
            </div>
            <br>
            HTML;
            return;
        }

        // VALIDAÇÃO: Verificar se usuário tem saldo suficiente
        $getAccountBalance = $db->query("SELECT `coins_transferable` FROM `accounts` WHERE `id` = {$db->quote($idLogged)}");
        $accountBalance = $getAccountBalance->fetch();
        $totalCost = $bid_max + $charbazaar_bid; // verificação do valor mínimo real

        if ($accountBalance['coins_transferable'] < $totalCost) {
            echo <<<HTML
            <div class="SmallBox">
                <div class="MessageContainer">
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeLeftTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeRightTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="Message">
                    <div class="BoxFrameVerticalLeft" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <div class="BoxFrameVerticalRight" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <table class="HintBox">
                        <tbody>
                        <tr>
                        <td>
                            <p style="color: #b32d2d; font-weight: bold; text-align: center; margin: 0;">
                            Saldo insuficiente. Necessário: {$totalCost} coins.
                            </p>
                        </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeRightBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeLeftBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                </div>
            </div>
            <br>
            HTML;
            return;
        }

        // uso de TRANSAÇÃO para evitar condições de corrida
        $db->beginTransaction();

        try {
            if ($countAuctionBid > 0) {
                // OLD BID ACCOUNT RETURN COINS - CORRIGIDO: usar conta do bid anterior
                $getAccountOldBid = $db->query("SELECT `coins_transferable` FROM `accounts` WHERE `id` = {$db->quote($getAuctionBid['account_id'])}");
                $getAccountOldBid = $getAccountOldBid->fetch();
                $SomaCoinsOldBid = $getAccountOldBid['coins_transferable'] + $getAuctionBid['bid'];
                $UpdateAccountOldBid = $db->exec('UPDATE `accounts` SET `coins_transferable` = ' . $db->quote($SomaCoinsOldBid) . ' WHERE `id` = ' . $db->quote($getAuctionBid['account_id']));

                // NEW BID ACCOUNT REMOVE COINS - CORRIGIDO: usar saldo atual
                $getAccountNewBid = $db->query("SELECT `coins_transferable` FROM `accounts` WHERE `id` = {$db->quote($idLogged)}");
                $getAccountNewBid = $getAccountNewBid->fetch();
                $SubCoinsNewBid = $getAccountNewBid['coins_transferable'] - $bid_max;
                $TaxCoinsNewBid = $SubCoinsNewBid - $charbazaar_bid; // TAX TO CREATE BID
                $UpdateAccountNewBid = $db->exec('UPDATE `accounts` SET `coins_transferable` = ' . $db->quote($TaxCoinsNewBid) . ' WHERE `id` = ' . $db->quote($idLogged));

                // UPDATE AUCTION NEW BID
                $Update_Auction = $db->exec('UPDATE `myaac_charbazaar` SET `price` = ' . $db->quote($bid_max) . ', `bid_account` = ' . $db->quote($idLogged) . ', `bid_price` = ' . $db->quote($bid_max) . ' WHERE `id` = ' . $db->quote($getAuction['id']));

                // CORRIGIDO: INSERT ao invés de UPDATE na tabela de bids
                $Insert_NewBid = $db->exec('INSERT INTO `myaac_charbazaar_bid` (`account_id`, `auction_id`, `bid`) VALUES (' . $db->quote($idLogged) . ', ' . $db->quote($getAuction['id']) . ', ' . $db->quote($bid_max) . ')');

            } else {
                // NEW BID ACCOUNT REMOVE COINS - CORRIGIDO: usar saldo atual
                $getAccountNewBid = $db->query("SELECT `coins_transferable` FROM `accounts` WHERE `id` = {$db->quote($idLogged)}");
                $getAccountNewBid = $getAccountNewBid->fetch();
                $SubCoinsNewBid = $getAccountNewBid['coins_transferable'] - $bid_max;
                $TaxCoinsNewBid = $SubCoinsNewBid - $charbazaar_bid; // TAX TO CREATE BID
                $UpdateAccountNewBid = $db->exec('UPDATE `accounts` SET `coins_transferable` = ' . $db->quote($TaxCoinsNewBid) . ' WHERE `id` = ' . $db->quote($idLogged));

                // UPDATE AUCTION NEW BID
                $Update_Auction = $db->exec('UPDATE `myaac_charbazaar` SET `price` = ' . $db->quote($bid_max) . ', `bid_account` = ' . $db->quote($idLogged) . ', `bid_price` = ' . $db->quote($bid_max) . ' WHERE `id` = ' . $db->quote($getAuction['id']));

                // INSERT NEW BID
                $Insert_NewBid = $db->exec('INSERT INTO `myaac_charbazaar_bid` (`account_id`, `auction_id`, `bid`) VALUES (' . $db->quote($idLogged) . ', ' . $db->quote($getAuction['id']) . ', ' . $db->quote($bid_max) . ')');
            }

            // Confirma todas as operações
            $db->commit();

        } catch (Exception $e) {
            // Desfaz todas as operações em caso de erro
            $db->rollback();
            echo <<<HTML
            <div class="SmallBox">
                <div class="MessageContainer">
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeLeftTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeRightTop" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="Message">
                    <div class="BoxFrameVerticalLeft" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <div class="BoxFrameVerticalRight" style="background-image:url(templates/tibiacom/images/global/content/box-frame-vertical.gif);"></div>
                    <table class="HintBox">
                        <tbody>
                        <tr>
                        <td>
                            <p style="color: #b32d2d; font-weight: bold; text-align: center; margin: 0;">
                            Erro interno. Tente novamente.
                            </p>
                        </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <div class="BoxFrameHorizontal" style="background-image:url(templates/tibiacom/images/global/content/box-frame-horizontal.gif);"></div>
                    <div class="BoxFrameEdgeRightBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                    <div class="BoxFrameEdgeLeftBottom" style="background-image:url(templates/tibiacom/images/global/content/box-frame-edge.gif);"></div>
                </div>
            </div>
            <br>
            HTML;
            return;
        }

        header('Location: ' . BASE_URL . '?subtopic=currentcharactertrades');
    }
}
?>

<?php
//DESATIVADO - Atualmente está sem uso devido ao bazaar semiauto
//Remova os comentários abaixo e desative a mecânica para reestabelecer o uso.
// if ($getPageAction == 'finish') {
//     $auction_iden = $_POST['auction_iden'];

//     /* GET INFO AUCTION */
//     $getAuction = $db->query("SELECT `id`, `account_old`, `account_new`, `player_id`, `price`, `date_end`, `date_start`, `bid_account`, `status` FROM `myaac_charbazaar` WHERE `id` = {$db->quote($auction_iden)}");
//     $getAuction = $getAuction->fetch();
//     /* GET INFO AUCTION END */

//     /* GET INFO BID */
//     $getBid = $db->query("SELECT `id`, `account_id`, `auction_id`, `bid`, `date` FROM `myaac_charbazaar_bid` WHERE `auction_id` = {$getAuction['id']}");
//     $getBid = $getBid->fetch();
//     /* GET INFO BID END */

//     /* GET COINS VENDEDOR */
//     $getCoinsVendedor = $db->query("SELECT `id`, `premdays`, `coins`, `coins_transferable` FROM `accounts` WHERE `id` = {$getAuction['account_old']}");
//     $getCoinsVendedor = $getCoinsVendedor->fetch();
    /* GET COINS VENDEDOR END */

//    $auction_taxacoins = $getBid['bid'] / 100;
//    $auction_taxacoins = $auction_taxacoins * $config['bazaar_tax'];
//    $auction_finalcoins = $getBid['bid'] - $auction_taxacoins;
    // $sellerCoins = $getCoinsVendedor['coins_transferable'] + ($getBid['bid'] - (($getBid['bid'] / 100) * $charbazaar_tax));
    // $db->exec("UPDATE `accounts` SET `coins_transferable` = {$sellerCoins} WHERE `id` = {$getAuction['account_old']}"); // adiciona os coins ao vendedor

    // $account_new = $getBid['account_id'];

    // $db->exec("UPDATE `players` SET `account_id` = {$account_new} WHERE `id` = {$getAuction['player_id']}"); // muda o player de conta
    // $db->exec("UPDATE `myaac_charbazaar` SET `status` = 1, `account_new` = {$account_new} WHERE `id` = {$getAuction['id']}"); // muda status da auction

    // header('Location: ' . BASE_URL . '?account/manage');
// }
// ?>
