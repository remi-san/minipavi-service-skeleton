<?php

namespace App;

use MiniPaviService\Message\Message;

/**
 * Messages the service shows, defined in config/messages.xml (pages can redefine them)
 */
enum AppMessage: string implements Message {
    // Envoi without a name
    case NameMissing = 'name.missing';
}
