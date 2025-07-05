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
        cart.value = {}

        toast("Sepetiniz Temizlendi", {
            "theme": "dark",
            "type": "info",
            "dangerouslyHTMLString": true
        })
    })

    const addToCart = async (product) => {
        console.log({product:product})
        const variantId = product.variantId;
        const key = `${product.id}-${variantId}`;

        if (cart.value[key]) {
            // quantity arttır
            cart.value[key].quantity++;
        } else {
            // yeni ürün ekle
            cart.value[key] = {
                ...product,
                id: product.id,
                variantId,
                quantity: 1,
            };
        }

        cart.value = { ...cart.value };

        try {
            const res = await axios.post('/add-product', {
                cart: JSON.stringify(cart.value),
                productId: product.id,
                variantId: variantId
            });

            toast(res?.data?.message || 'Bir hata oluştu', {
                theme: "dark",
                type: "success",
                dangerouslyHTMLString: true
            });
            console.log({ res });
        } catch (err) {
            toast(err.response?.data?.message || 'Bir hata oluştu', {
                theme: "dark",
                type: "warning",
                dangerouslyHTMLString: true
            });
        }
    }

    const decreaseFromCart = async (product) => {
        console.log({sf:product})
        const variantId = product.variantId;
        const key = `${product.id}-${variantId}`;

        console.log({key: key})
        if (cart.value[key]) {
            cart.value[key].quantity--;
            if (cart.value[key].quantity <= 0) {
                delete cart.value[key];
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
        cart.value
            ? Object.values(cart.value).reduce((sum, item) => {
                const variant = item.variants?.find(v => v.id === item.variantId)
                const price = variant ? parseFloat(variant.price) : 0
                return sum + item.quantity * price
            }, 0)
            : 0
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
