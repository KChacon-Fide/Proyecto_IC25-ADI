<?php


function renderOrderCard($order) {
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'cooking' => 'bg-blue-100 text-blue-800 border-blue-200',
        'ready' => 'bg-green-100 text-green-800 border-green-200',
    ];

    $statusLabels = [
        'pending' => 'Pendiente',
        'cooking' => 'Preparando',
        'ready' => 'Listo',
    ];

    $statusIcons = [
        'pending' => '<i class="fa fa-exclamation-circle h-4 w-4 mr-1"></i>',
        'cooking' => '<i class="fa fa-utensils h-4 w-4 mr-1"></i>',
        'ready' => '<i class="fa fa-check-circle h-4 w-4 mr-1"></i>',
    ];

    $timeAgo = formatTimeAgo($order['orderTime']);

    $isExpanded = false; // Control de expansión en PHP

    $cardClass = 'overflow-hidden border-l-4 ' . (
        $order['status'] === 'pending'
            ? 'border-l-yellow-500'
            : ($order['status'] === 'cooking'
                ? 'border-l-blue-500'
                : 'border-l-green-500')
    );

    $contentClass = 'space-y-2 ' . (!$isExpanded && count($order['items']) > 2 ? 'max-h-32 overflow-hidden relative' : '');

    $content = '';
    foreach ($order['items'] as $index => $item) {
        $content .= '<div class="border-b border-gray-100 pb-2 last:border-0">
                        <div class="flex justify-between">
                            <span class="font-medium">' . $item['name'] . '</span>
                            <span class="text-sm bg-gray-100 px-2 py-0.5 rounded-full">x' . $item['quantity'] . '</span>
                        </div>';
        if (!empty($item['observations'])) {
            $content .= '<p class="text-sm text-gray-600 mt-1"><span class="font-medium">Nota:</span> ' . $item['observations'] . '</p>';
        }
        $content .= '</div>';
    }

    $toggleButton = '';
    if (count($order['items']) > 2) {
        $toggleButton = '<button onclick="toggleExpand()" class="text-sm text-primary hover:underline mt-2">' . ($isExpanded ? 'Ver menos' : 'Ver todos los items') . '</button>';
    }

    return '
        <div class="' . $cardClass . '">
            <div class="pb-2">
                <div class="flex justify-between items-start">
                    <h1 class="text-xl">' . $order['table'] . '</h1>
                    <span class="' . $statusColors[$order['status']] . ' flex items-center">
                        ' . $statusIcons[$order['status']] . '
                        ' . $statusLabels[$order['status']] . '
                    </span>
                </div>
                <div class="flex items-center text-sm text-gray-500 mt-1">
                    <i class="fa fa-clock h-4 w-4 mr-1"></i>
                    <span>' . $timeAgo . ' (' . date('H:i', strtotime($order['orderTime'])) . ')</span>
                </div>
            </div>
            <div>
                <div class="' . $contentClass . '">' . $content . '</div>
                ' . $toggleButton . '
            </div>
            <div class="flex justify-between pt-2 pb-4">
                ' . renderFooterButtons($order) . '
            </div>
        </div>
    ';
}

function renderFooterButtons($order) {
    $buttons = '';
    if ($order['status'] === 'pending') {
        $buttons .= '<button onclick="onUpdateStatus(' . $order['id'] . ', \'cooking\')" class="w-full">Comenzar preparación</button>';
    } elseif ($order['status'] === 'cooking') {
        $buttons .= '<button onclick="onUpdateStatus(' . $order['id'] . ', \'ready\')" class="w-full">Marcar como listo</button>';
    } elseif ($order['status'] === 'ready') {
        $buttons .= '<button onclick="onUpdateStatus(' . $order['id'] . ', \'pending\')" class="w-full">Volver a pendiente</button>';
    }
    return $buttons;
}

function formatTimeAgo($dateTime) {
    // Implementa la lógica para formatear el tiempo transcurrido
    return 'hace ' . timeAgo($dateTime); // Por ejemplo
}

// Función para simular el tiempo transcurrido
function timeAgo($dateTime) {
    $now = new DateTime();
    $past = new DateTime($dateTime);
    $interval = $now->diff($past);

    if ($interval->y > 0) {
        return $interval->y . ' año' . ($interval->y > 1 ? 's' : '');
    } elseif ($interval->m > 0) {
        return $interval->m . ' mes' . ($interval->m > 1 ? 'es' : '');
    } elseif ($interval->d > 0) {
        return $interval->d . ' día' . ($interval->d > 1 ? 's' : '');
    } elseif ($interval->h > 0) {
        return $interval->h . ' hora' . ($interval->h > 1 ? 's' : '');
    } elseif ($interval->i > 0) {
        return $interval->i . ' minuto' . ($interval->i > 1 ? 's' : '');
    } else {
        return 'unos segundos';
    }
}

function toggleExpand() {
    // Implementa la lógica para expandir o colapsar los items
}
