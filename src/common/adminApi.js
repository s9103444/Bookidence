import axios from "axios";
import {API_BASE} from "@/common/api.js"
import {useAdminStore} from "@/stores/adminAuth.js"
import router from "@/router"

export const adminApi = axios.create({
    baseURL:API_BASE,
    });

adminApi.interceptors.request.use((config) => {
  const adminStore = useAdminStore()
  if (adminStore.token) {
    config.headers.Authorization = `Bearer ${adminStore.token}`
  }
  return config
})

adminApi.interceptors.response.use(
    (res)=>res,
    (err)=>{
        const isAuthApi=err.config?.url?.includes('admin_login.php')
        if(err.response?.status===401 && !isAuthApi){
            useAdminStore().logout()
            router.push({name:'admin-login'})
        }
        return Promise.reject(err)
    })

