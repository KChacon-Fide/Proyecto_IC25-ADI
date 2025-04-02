function toggleCalc() {
    const calc = document.getElementById('calculator');
    if (calc.style.display === 'none' || calc.style.display === '') {
        calc.style.display = 'block';
        setTimeout(() => {
            document.addEventListener('click', outsideClickListener);
        }, 0);
    } else {
        closeCalc();
    }
}

function closeCalc() {
    const calc = document.getElementById('calculator');
    calc.style.display = 'none';
    document.removeEventListener('click', outsideClickListener);
}

function outsideClickListener(e) {
    const calc = document.getElementById('calculator');
    const bubble = document.getElementById('calc-bubble');
    if (!calc.contains(e.target) && !bubble.contains(e.target)) {
        closeCalc();
    }
}

function append(val) {
    const display = document.getElementById('calc-display');
    const raw = display.getAttribute('data-raw') || '';
    const newRaw = raw + val;

    display.setAttribute('data-raw', newRaw);
    display.value = formatExpressionWithCommas(newRaw);
}

function calculate() {
    const display = document.getElementById('calc-display');
    const raw = display.getAttribute('data-raw') || display.value.replace(/,/g, '');

    try {
        const result = eval(raw);
        display.value = formatNumberWithCommas(result.toString());
        display.setAttribute('data-raw', result.toString());
    } catch {
        display.value = 'Error';
        display.removeAttribute('data-raw');
    }
}

function clearDisplay() {
    const display = document.getElementById('calc-display');
    display.value = '';
    display.removeAttribute('data-raw');
}

function formatNumberWithCommas(x) {
    const parts = x.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return parts.join('.');
}

function formatExpressionWithCommas(expression) {
    return expression.replace(/\d+(\.\d+)?/g, (num) => formatNumberWithCommas(num));
}

document.addEventListener('keydown', function (e) {
    const calc = document.getElementById('calculator');
    if (calc.style.display !== 'none') {
        const key = e.key;
        const display = document.getElementById('calc-display');
        let raw = display.getAttribute('data-raw') || '';

        if (!isNaN(key) || ['+', '-', '*', '/', '.'].includes(key)) {
            raw += key;
            display.setAttribute('data-raw', raw);
            display.value = formatExpressionWithCommas(raw);
        } else if (key === 'Enter') {
            calculate();
        } else if (key === 'Backspace') {
            raw = raw.slice(0, -1);
            display.setAttribute('data-raw', raw);
            display.value = formatExpressionWithCommas(raw);
        } else if (key.toLowerCase() === 'c') {
            clearDisplay();
        }
    }
});
