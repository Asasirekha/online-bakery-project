let cart = [];

function addToCart(name, price) {
    const quantityInput = document.querySelector(`[name='quantity']`);
    if (!quantityInput) {
        alert('Quantity input not found.');
        return;
    }

    const quantity = parseInt(quantityInput.value, 10);
    if (isNaN(quantity) || quantity <= 0) {
        alert('Please enter a valid quantity.');
        return;
    }

    const item = { name, price, quantity };
    cart.push(item);
    updateCartDisplay();
}

function updateCartDisplay() {
    let cartHTML = '';
    let total = 0;
    const cartItemsList = document.getElementById('cart-items');
    if (!cartItemsList) {
        alert('Cart items list not found.');
        return;
    }

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        cartHTML += `<li>${item.name} - &#8377;${item.price} x ${item.quantity} = &#8377;${itemTotal}</li>`;
    });

    cartHTML += `<p><strong>Total:</strong> &#8377;${total}</p>`;
    cartItemsList.innerHTML = cartHTML;
}

function placeOrder() {
    if (cart.length === 0) {
        alert('Your cart is empty.');
        return;
    }

    const phone = prompt('Enter your mobile number:');
    if (!phone) {
        alert('Please enter a valid phone number.');
        return;
    }

    // Create and send an AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'order.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                let orderSummary = 'Order Details:\n';
                response.orderDetails.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    orderSummary += `Item: ${item.name}, Price: &#8377;${item.price}, Quantity: ${item.quantity}, Total: &#8377;${itemTotal}\n`;
                });
                orderSummary += `\nPhone: ${response.phone}\n`;
                orderSummary += `Name: ${response.userDetails.fullname}\n`;
                orderSummary += `Address: ${response.userDetails.address}\n`;

                // Display order summary and redirect
                if (confirm(orderSummary + '\nWould you like to view your order details?')) {
                    window.location.href = 'order_confir.php'; // Redirect to your PHP page
                }

                cart = [];
                updateCartDisplay();
            } else {
                alert(response.error || 'Failed to place the order.');
            }
        } else {
            alert(`Failed to place the order. Status: ${xhr.status}`);
        }
    };

    xhr.onerror = function() {
        alert('An error occurred during the request.');
    };

    // Send data to server
    const cartData = JSON.stringify(cart);
    xhr.send(`cart=${encodeURIComponent(cartData)}&phone=${encodeURIComponent(phone)}`);
}
