import { defineStore } from "pinia";
import {API_BASE} from "@/common/api.js"

export const useAdminStore = defineStore("admin",{
    state:()=>({
        isLoggedIn:false,
        token:null,
        account:null,
        staffName:"",
    }),
    actions:{
        login(data){
            this.isLoggedIn=true;
            this.token=data.token;
            this.account=data.account;
            this.staffName=data.staffName;
        },
        logout(){
            const token=this.token
            this.isLoggedIn=false;
            this.token=null;
            this.account=null;
            this.staffName="";

            if(token){
                fetch(`${API_BASE}/admin_logout.php`,{
                    method:'POST',
                    headers:{Authorization: `Bearer ${token}`},
                })

            }
        },
        async restoreSession(){
            if(!this.token){
                return;
            }
            const res=await fetch(`${API_BASE}/admin_me.php`,{
                headers:{Authorization:`Bearer ${this.token}`},
            });
            const result = await res.json();
            if(!result.success){
                this.logout();
                return;
            }
            this.login({
                token:this.token,
                account:result.staff.staff_account,
                staffName:result.staff.staff_name,
            });
        }
    },
    persist:{
        pick:["token"],
    }
});