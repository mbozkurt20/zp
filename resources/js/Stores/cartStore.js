import { defineStore } from 'pinia'
import { ref,computed } from 'vue'
import axios from "axios";

export const useCartStore = defineStore('cart', () => {
    const cart = ref({})

    const addToCart = async (product) => {
        if (!cart.value[product.id]) {
            cart.value[product.id] = { ...product, quantity: 1 }
        } else {
            cart.value[product.id].quantity++
        }
        try {
            const response = await axios.post('/add-product', { cart: JSON.stringify(cart.value)});
            console.log({ response });
        } catch (error) {
            console.error('Error adding to cart:', error);
        }
    }

    const decreaseFromCart = async (product) => {
        if (cart.value[product.id]) {
            cart.value[product.id].quantity--
            if (cart.value[product.id].quantity <= 0) {
                delete cart.value[product.id]
            }
        }

        try {
            const response = await axios.post('/remove-product', { cart: JSON.stringify(cart.value)});
            console.log({ response });
        } catch (error) {
            console.error('Error adding to cart:', error);
        }
    }

    const clearCart = () => {
        cart.value = {}
    }

    const totalItems = computed(() =>
        Object.values(cart.value).reduce((sum, item) => sum + item.quantity, 0)
    )

    const totalProduct = computed(() =>
        Object.values(cart.value).length
    )

    const totalAmount = computed(() =>
        Object.values(cart.value).reduce((sum, item) => sum + item.quantity * item.price, 0)
    )

    return {
        cart,
        addToCart,
        decreaseFromCart,
        clearCart,
        totalItems,
        totalAmount,
        totalProduct
    }
})
