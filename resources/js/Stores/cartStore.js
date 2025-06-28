import Pusher from 'pusher-js'
import { defineStore } from 'pinia'
import { ref,computed } from 'vue'
import axios from "axios";
import {toast} from "vue3-toastify";
const userId = window.Laravel?.userId;

export const useCartStore = defineStore('cart', () => {
    console.log({auth:userId })
    const cart = ref({})

    // Pusher setup
    const pusher = new Pusher('ac293c727687682a5b63', {
        cluster: 'eu',
    })

    const channel = pusher.subscribe('cart-channel') // kanal ismi
    channel.bind(`clear-cart-${userId}`, () => { // event ismi
        clearCart()
        toast.info("Sepetiniz başka bir işlem tarafından boşaltıldı.", {
            autoClose: 3000
        })
    })

    const addToCart = async (product) => {
        if (!cart.value[product.id]) {
            cart.value[product.id] = { ...product, quantity: 1 }

            try {
                const response = await axios.post('/add-product', { cart: JSON.stringify(cart.value) });
                console.log({ response });
            } catch (error) {
                console.error('Error removing from cart:', error);
            }
        } else {
            if (cart.value[product.id].quantity < product.quantity) {
                cart.value[product.id].quantity++

                try {
                    const response = await axios.post('/add-product', { cart: JSON.stringify(cart.value) });
                    console.log({ response });
                } catch (error) {
                    console.error('Error removing from cart:', error);
                }
            }
        }
    }

    const decreaseFromCart = async (product) => {
        if (cart.value[product.id]) {
            cart.value[product.id].quantity--
            if (cart.value[product.id].quantity <= 0) {
                delete cart.value[product.id]
            }

            try {
                const response = await axios.post('/remove-product', { cart: JSON.stringify(cart.value) });
                console.log({ response });
            } catch (error) {
                console.error('Error removing from cart:', error);
            }
        }
    }
    const loadCart = async () => {
        try {
            const res = await axios.get('/active/basket')

            console.log({carttt:res})
            if (res.data?.cart) {
                cart.value = res.data.cart
            }
        } catch (error) {
            console.error('Sepet yüklenirken hata oluştu:', error)
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

    loadCart()

    return {
        cart,
        addToCart,
        decreaseFromCart,
        clearCart,
        totalItems,
        totalAmount,
        totalProduct,
        loadCart
    }
})
