<?php

declare(strict_types=1);

namespace broki\trashcanplus\command;

use broki\trashcanplus\Trashcan;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use Ramsey\Uuid\Uuid;

class TrashcanCommand extends Command implements PluginOwned {

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return true;
        }

        if (!($sender instanceof Player)) {
            return false;
        }

        if (isset($args[0])) {
            switch (strtolower($args[0])) {
                case "item":
                case "get":
                    if (!$sender->hasPermission("trashcan.get")) {
                        return true;
                    }

                    $trashcanItem = ItemFactory::getInstance()->get(ItemIds::CAULDRON)->setNamedTag(CompoundTag::create()->setInt("trashcan_item", 1)->setString("id", Uuid::uuid4()->toString()));
                    $sender->getInventory()->addItem($trashcanItem->setCustomName(TextFormat::RESET . TextFormat::WHITE . "Trashcan"));
                    break;
                case "spawn":
                case "create":
                    if (!$sender->hasPermission("trashcan.spawn")) {
                        return true;
                    }

                    $sender->sendMessage("[Trashcan]" . TextFormat::GREEN . " Trashcan successfully spawned!");
                Trashcan::getInstance()->spawnTrashcan($sender->getLocation(), $args[1] ?? null, $sender->getXuid());
                    break;
                case "despawn":
                case "remove":
                    if (!$sender->hasPermission("trashcan.despawn")) {
                        return true;
                    }

                    $sender->sendMessage("[Trashcan]" . TextFormat::RED . " Tap the trashcan you want to despawn");
                    Trashcan::getInstance()->listWhoWannaDespawnTrashcan[] = $sender->getUniqueId()->toString();
                    break;
                case "help":
                    $sender->sendMessage("\n§7---- ---- ---- - ---- ---- ----\n§eCommand List:\n§2» /trashcan get\n§2» /trashcan spawn <optional: nametag>\n§2» /trashcan despawn\n§2» /trashcan help\n§7---- ---- ---- - ---- ---- ----");
                    break;
                default:
                    $sender->sendMessage(TextFormat::RED . "Subcommand '$args[0]' not found! Try '/trashcan help' for help.");
                    break;
            }
        } else {
            $sender->sendMessage("§7---- ---- [ §3TrashcanPlus§7 ] ---- ----\n§bAuthor: @brokiem\n§3Source Code: github.com/brokiem/Trashcan\nVersion " . $this->getOwningPlugin()->getDescription()->getVersion() . "\n§7---- ---- ---- - ---- ---- ----");
        }

        return false;
    }

    public function getOwningPlugin(): Plugin {
        return Trashcan::getInstance();
    }
}