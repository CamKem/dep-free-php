export default class Cart {
    constructor(taxRate, shippingRate) {
        this.taxRate = taxRate;
        this.shippingRate = shippingRate;

        this.handleQuantityChange()
    }

    handleQuantityChange() {
        const inputs = Array.from(document.querySelectorAll('.qty'));
        inputs.forEach((input) => {
            input.addEventListener('input', (event) => {
                const productId = event.target.parentNode.querySelector('input[name="product_id"]').value
                const quantity = event.target.value
                if (quantity < 1) {
                    event.target.value = 1
                    this.updateQuantity(input, 1)
                    return
                }
                fetch('/cart', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity,
                    }),
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json().then((data) => {
                            const flashEvent = new CustomEvent('flashToggle', {
                                bubbles: true,
                                detail: {message: data.message}
                            })
                            document.dispatchEvent(flashEvent)
                            this.updateQuantity(input, quantity)
                        })
                    })
            })
        })
    }

    recalculateCart() {
        let runningTotal = Cart.calcRunningTotal(0);

        const taxPrice = runningTotal * this.taxRate
        const totalPrice = runningTotal + taxPrice + this.shippingRate

        document.getElementById('subTotalPrice').textContent = `$${Number(
            runningTotal.toFixed(2))
            .toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})
        }`;
        document.getElementById('taxPrice').textContent = `$${Number(
            taxPrice.toFixed(2))
            .toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})
        }`;
        document.getElementById('totalPrice').textContent = `$${Number(
            totalPrice.toFixed(2))
            .toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})
        }`;
    }

    static calcRunningTotal(runningTotal) {
        Array.from(document.querySelectorAll('.items')).forEach((row) => {
            let price = row.children.namedItem('prodTotal').children.namedItem('line-price').children[0].textContent.slice(1)
            runningTotal += parseFloat(price.replace(/,/g, ''))

        })
        return runningTotal;
    }

    updateQuantity(input, quantity) {
        const productRow = input.parentNode.parentNode
        let textPrice = productRow.children[0].children.namedItem('price').children[1].textContent
        textPrice = textPrice.slice(1)
        const price = parseFloat(textPrice)
        const newLinePrice = price * quantity
        const linePriceElement = productRow.parentNode.children.namedItem('prodTotal').children.namedItem('line-price').children[0];
        linePriceElement.textContent = `$${newLinePrice.toFixed(2)}`
        this.recalculateCart()
    }
}