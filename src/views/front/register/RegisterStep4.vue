<script>
  const mockAppearOptions = [
    // 性別
    { appearId: 'g01', type: 'gender', optionName: '女生', value: 'female' },
    { appearId: 'g02', type: 'gender', optionName: '男生', value: 'male' },

    // 髮色
    { appearId: 'fh1', type: 'hair', optionName: '女黑髮', gender: 'female', iconPath: '/src/assets/images/appear/female_hair_black.png' },
    { appearId: 'fh2', type: 'hair', optionName: '女藍髮', gender: 'female', iconPath: '/src/assets/images/appear/female_hair_blue.png' },
    { appearId: 'fh3', type: 'hair', optionName: '女咖髮', gender: 'female', iconPath: '/src/assets/images/appear/female_hair_brown.png' },
    { appearId: 'mh1', type: 'hair', optionName: '男黑髮', gender: 'male', iconPath: '/src/assets/images/appear/male_hair_black.png' },
    { appearId: 'mh2', type: 'hair', optionName: '男藍髮', gender: 'male', iconPath: '/src/assets/images/appear/male_hair_blue.png' },
    { appearId: 'mh3', type: 'hair', optionName: '男咖髮', gender: 'male', iconPath: '/src/assets/images/appear/male_hair_brown.png' },

    // 瞳色
    { appearId: 'fe1', type: 'eyes', optionName: '女黑眼', gender: 'female', iconPath: '/src/assets/images/appear/female_eyes_black.png' },
    { appearId: 'fe2', type: 'eyes', optionName: '女藍眼', gender: 'female', iconPath: '/src/assets/images/appear/female_eyes_blue.png' },
    { appearId: 'fe3', type: 'eyes', optionName: '女綠眼', gender: 'female', iconPath: '/src/assets/images/appear/female_eyes_green.png' },
    { appearId: 'me1', type: 'eyes', optionName: '男黑眼', gender: 'male', iconPath: '/src/assets/images/appear/male_eyes_black.png' },
    { appearId: 'me2', type: 'eyes', optionName: '男藍眼', gender: 'male', iconPath: '/src/assets/images/appear/male_eyes_blue.png' },
    { appearId: 'me3', type: 'eyes', optionName: '男綠眼', gender: 'male', iconPath: '/src/assets/images/appear/male_eyes_green.png' },

    // 膚色
    { appearId: 'fs1', type: 'skin', optionName: '女白皮', gender: 'female', iconPath: '/src/assets/images/appear/female_skin_light.png' },
    { appearId: 'fs2', type: 'skin', optionName: '女黃皮', gender: 'female', iconPath: '/src/assets/images/appear/female_skin_medium.png' },
    { appearId: 'fs3', type: 'skin', optionName: '女深皮', gender: 'female', iconPath: '/src/assets/images/appear/female_skin_dark.png' },
    { appearId: 'ms1', type: 'skin', optionName: '男白皮', gender: 'male', iconPath: '/src/assets/images/appear/male_skin_light.png' },
    { appearId: 'ms2', type: 'skin', optionName: '男黃皮', gender: 'male', iconPath: '/src/assets/images/appear/male_skin_medium.png' },
    { appearId: 'ms3', type: 'skin', optionName: '男深皮', gender: 'male', iconPath: '/src/assets/images/appear/male_skin_dark.png' },
  ];
  export default {
    props:{
      selectedGender: {
        type: String,
      },
      selectedHairColorId:{
        type: String,
      },
      selectedSkinColorId:{
        type: String,
      },
      selectedEyeColorId:{
        type: String,
      },
    },
    data() {
      return {
        allAppearOptions: []   // 存放「API 回來」的完整清單
      };
    },
    created() {
      this.allAppearOptions = mockAppearOptions
    },
    computed:{
      hairOptions() {
        return this.allAppearOptions.filter(option => option.type === "hair" && option.gender === this.selectedGender);
      },
      skinOptions(){
        return this.allAppearOptions.filter(option => option.type === "skin" && option.gender === this.selectedGender);
      },
      eyeOptions(){
        return this.allAppearOptions.filter(option => option.type === "eyes" && option.gender === this.selectedGender);
      },
    }
  };
</script>

<template>
  <div>Step4 佔位,尚未實作</div>
</template>