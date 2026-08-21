import { defineStore } from "pinia";
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
            // console.log(data)
        },
        logout(){
            this.isLoggedIn=false;
            this.token=null;
            this.account=null;
            this.staffName="";
        }
    },
    persist:{
        pick:["token"],
    }
});