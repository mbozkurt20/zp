import Pusher from 'pusher-js'
import { defineStore } from 'pinia'
import { ref,computed } from 'vue'
import axios from "axios";
import {toast} from "vue3-toastify";
import Swal from 'sweetalert2'

const userId = window.Laravel?.userId;

export const useCartStore = defineStore('cart', () => {
    const cart = ref({})

    // Pusher setup
    const pusher = new Pusher('ac293c727687682a5b63', {
        cluster: 'eu',
    })

  const channel = pusher.subscribe('cart-channel') // kanal ismi
    channel.bind(`clear-cart-${userId}`, () => { // event ismi
        clearCart()

        toast("Sepetiniz Temizlendi", {
            "theme": "dark",
            "type": "info",
            "dangerouslyHTMLString": true
        })
    })

    const addToCart = async (product) => {
        console.log({product:product})
        if (!cart.value[product.id]) {
            await axios.post('/add-product', { cart: JSON.stringify(cart.value), productId: product.id }).then(res => {
                console.log({res: res})
                cart.value[product.id] = { ...product, quantity: 1 }
            }).catch(err => {
                console.log({error1: err})

                toast(err.response.data.message, {
                    "theme": "dark",
                    "type": "warning",
                    "dangerouslyHTMLString": true
                })
            })
        } else {
            console.log({product:product})
            await axios.post('/add-product', { cart: JSON.stringify(cart.value), productId: product.id }).then(res => {
                console.log({res: res})
                cart.value[product.id].quantity++

            }).catch(err => {
                console.log({error1: err})
                toast(err.response.data.message, {
                    "theme": "dark",
                    "type": "warning",
                    "dangerouslyHTMLString": true
                })
            })
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
console.log({sepet:res.data})
            cart.value = res.data.cart
        } catch (error) {
            console.error('Sepet yüklenirken hata oluştu:', error)
        }
    }

    const clearCart =  () => {
        Swal.fire({
            title: 'Sepeti temizlemek istiyor musunuz?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Evet, temizle',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {

                axios.post('/clear-cart', { cart: JSON.stringify(cart.value) }).then(res => {
                    cart.value = {}
                    Swal.fire('Temizlendi!', 'Sepetiniz boşaltıldı.', 'success')
                }).catch(err => {
                    toast('Sepetiniz anlık bir hatadan temizlenemedi!!', {
                        "theme": "dark",
                        "type": "error",
                        "dangerouslyHTMLString": true
                    })
                })

            }
        })
    }

    const totalItems = computed(() =>
        cart.value ? Object.values(cart.value).reduce((sum, item) => sum + item.quantity, 0)  :null
    )

    const totalProduct = computed(() =>
        cart.value ?  Object.values(cart.value).length :null
    )

    const totalAmount = computed(() =>
        cart.value ? Object.values(cart.value).reduce((sum, item) => sum + item.quantity * item.price, 0) : null
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
