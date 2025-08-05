<?php global $db, $account_logged;
/**
 * Add Account Coins
 *
 * @package   MyAAC
 * @author    João Paulo (Tryller/jprzimba)
 * @copyright 2023 MyAAC
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Add Tibia Coins';
$base = BASE_URL . 'admin/?p=add_account_coins';

function echo_success($message)
{
    echo '<p class="success">' . $message . '</p>';
}

function echo_error($message)
{
    global $error;
    echo '<p class="error">' . $message . '</p>';
    $error = true;
}

$query = $db->query("SELECT `id`, `name`, `email`, `coins`, `coins_transferable` FROM `accounts` WHERE ID > 1;");
$accounts = $query->fetchAll();

if (isset($_POST['add_coins']) && $account_logged->isSuperAdmin()) {
    $coinsToAdd = (int)$_POST['coins_'] ?? 0;
    $coinType = $_POST['coin_type'] ?? 'coins';

    if ($coinsToAdd < 1) {
        echo_error("You need to add 1 or more Tibia Coins!");
    } else {
        $coinColumn = $coinType === 'transferable' ? 'coins_transferable' : 'coins';
        try {
            foreach ($accounts as $acc) {
                $newCoins = (int)$acc[$coinColumn] + $coinsToAdd;
                $db->exec("UPDATE `accounts` SET `{$coinColumn}` = {$newCoins} WHERE `id` = {$acc['id']}");
            }
            echo_success("You have added {$coinsToAdd} Tibia Coins ({$coinType}) to all accounts.");
            $accounts = $db->query("SELECT `id`, `name`, `email`, `coins`, `coins_transferable` FROM `accounts` WHERE ID > 1;")->fetchAll();
        } catch (PDOException $error) {
            echo_error($error->getMessage());
        }
    }
}
?>
<div class="row">
    <div class="col-12 col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Add Tibia Coins to ALL Accounts</h3><br>
                <span>How many coins do you want to add?</span>
                <?php if (count($accounts) > 0 && $account_logged->isSuperAdmin()) { ?>
                    <div class="box mt-4">
                        <div class="box-body">
                            <form action="<?= $base ?>" method="post">
                                <div class="form-group">
                                    <div class="input-group justify-content-between">
                                        <div class="form-group">
                                            <small>Insert the number of coins you want to add for all accounts.</small><br>
                                            <input type="number" name="coins_" min="1" max="9999" placeholder="(min 1, max 9999)" style="width: 140px;">
                                        </div>
                                        <div>
                                            <small>Select the type of coin:</small><br>
                                            <select name="coin_type" class="btn btn-primary dropdown-toggle">
                                                <option value="coins">Non-Transferable</option>
                                                <option value="transferable">Transferable</option>
                                            </select>
                                        </div>
                                        <div class="input-group-btn d-flex align-items-end">
                                            <button type="submit" class="btn btn-success" name="add_coins"><i class="fa fa-add"></i> ADD</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Accounts:</h3>
            </div>
            <div class="box-body no-padding">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Acc Name</th>
                        <th>E-mail</th>
                        <th style="text-align: right">Non-Transferable Coins</th>
                        <th style="text-align: right">Transferable Coins</th>
                    </tr>
                    <?php foreach ($accounts as $k => $acc) { ?>
                        <tr>
                            <td><?= $k + 1 ?></td>
                            <td><?= $acc['id'] ?></td>
                            <td><?= $acc['name'] ?></td>
                            <td><?= $acc['email'] ?></td>
                            <td style="text-align: right;"><?= $acc['coins'] ?></td>
                            <td style="text-align: right;"><?= $acc['coins_transferable'] ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
