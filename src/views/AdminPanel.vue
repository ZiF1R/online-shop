<template>
  <div class="wrapper">
    <template v-if="user?.isAdmin">
      <h1>Панель администратора</h1>
      <div class="actions" v-for="(item, i) in list" :key="i">
        <h2>{{item.name}}</h2>
        <div class="fields" id="fields">
          <select name="section_actions" v-model="listActiveActions[i]">
            <option v-for="action in item.actions" :key="action.value" :value="action">
              {{action.name}}
            </option>
          </select>
          <template v-for="field in listActiveActions[i].fields" :key="field.id">
            <CustomInput v-if="field?.type !== 'Select'" v-bind="field" v-model="field.value" />
            <select v-else v-model="field.value" @change="field?.onSelect && field?.onSelect(i)">
              <option selected :value="null">{{field?.defaultOption}}</option>
              <option v-for="option in field.options" :key="option.id" :value="option">
                {{option[field.name]}}
              </option>
            </select>
          </template>
          <template v-for="field in listActiveActions[i]?.additionalFields" :key="field.id">
            <CustomInput v-bind="field" v-model="field.value" />
          </template>
          <button @click="item.handler(i)">Подтвердить</button>
        </div>
      </div>
    </template>
    <p v-else>У Вас нет прав для доступа к этой странице!</p>
  </div>

  <AppModal v-if="openModal" @onClose="openModal = false">
    <template #header>
      <h2>Вы уверены?</h2>
    </template>
    <template #content>
      Отменить действие будет невозможно!
    </template>
    <template #buttons>
      <button @click="modalAction">Удалить</button>
    </template>
  </AppModal>
</template>

<script setup lang="ts">
import {useUserStore} from "stores/user";
import {computed, onMounted, reactive, ref, watch} from "vue";
import {useRouter} from "vue-router";
import CustomInput from "components/CustomElements/CustomInput.vue";
import {
  createCategory,
  createSection, deleteCategory,
  deleteSection,
  getAllBrands,
  getAllSections,
  getSectionCategories
} from "services/menu.service";
import {getCategoryFilters} from "services/categories.service";
import AppModal from "components/AppModal.vue";
import type {Product} from "types/main.types";
import {createProduct, deleteProduct} from "services/products.service";

const modalAction = ref(() => {});
const openModal = ref(false);
const sections = ref([]);
const categories = ref([]);
const brands = ref([]);

onMounted(async () => {
  await fetchSections();
  await fetchCategories();
  await fetchBrands();
});

async function fetchSections() {
  sections.value = await getAllSections();
}

async function fetchBrands() {
  brands.value = await getAllBrands();
}

async function fetchCategories() {
  categories.value = [];
  for (const section of sections.value) {
    categories.value = categories.value.concat(await getSectionCategories(+section.id));
  }
}

const router = useRouter();
const user = useUserStore();
const list = ref([
  {
    name: "Разделы",
    actions: [
      {
        value: "add",
        name: "Добавить",
        fields: [
          {
            id: 1,
            placeholder: "Название раздела",
            value: "",
          },
        ],
      },
      {
        value: "delete",
        name: "Удалить",
        fields: [
          {
            id: 1,
            type: "Select",
            defaultOption: "Выберите раздел",
            options: computed(() => sections.value),
            value: null,
            name: "name",
          },
        ],
      }
    ],
    handler: async (i: number) => {
      const action = listActiveActions.value[i];
      if (action.value === "add") {
        const name = action.fields[0].value;
        await createSection(name);
        await fetchSections();
      } else {
        const id = action.fields[0].value.id;
        openModal.value = true;
        modalAction.value = async () => {
          await deleteSection(+id);
          await fetchSections();
          openModal.value = false;
        };
      }
    },
  },
  {
    name: "Категории",
    actions: [
      {
        value: "add",
        name: "Добавить",
        fields: [
          {
            id: 1,
            type: "Select",
            defaultOption: "Выберите раздел",
            options: computed(() => sections.value),
            value: null,
            name: "name",
          },
          {
            id: 2,
            placeholder: "Название категории",
            value: "",
          },
        ],
      },
      {
        value: "delete",
        name: "Удалить",
        fields: [
          {
            id: 1,
            type: "Select",
            defaultOption: "Выберите категорию",
            options: computed(() => categories.value),
            value: null,
            name: "name",
          },
        ],
      }
    ],
    handler: async (i: number) => {
      const action = listActiveActions.value[i];
      if (action.value === "add") {
        const section_id = action.fields[0].value.id;
        const name = action.fields[1].value;
        await createCategory(+section_id, name);
        await fetchCategories();
      } else {
        const id = action.fields[0].value.id;
        openModal.value = true;
        modalAction.value = async () => {
          await deleteCategory(+id);
          await fetchCategories();
          openModal.value = false;
        };
      }
    },
  },
  {
    name: "Товары",
    actions: [
      {
        value: "add",
        name: "Добавить",
        additionalFields: [],
        fields: [
          {
            id: 1,
            placeholder: "Название",
            value: "",
            column: "name",
          },
          {
            id: 2,
            type: "number",
            placeholder: "Код",
            value: "",
            column: "code",
          },
          {
            id: 3,
            type: "Select",
            defaultOption: "Выберите категорию",
            options: computed(() => categories.value),
            value: null,
            name: "name",
            column: "category_id",
            onSelect: async (i: number) => {
              const fields = list.value[i].actions[0]?.fields;
              const properties = await getProperties(+fields[2].value?.id);
              if (!properties) return;
              listActiveActions.value[i].additionalFields = properties.reduce((acc, prop) => {
                const property = {
                  id: fields.length + acc.length,
                  placeholder: prop.property.name,
                  value: "",
                  name: prop.property.name,
                  column: "value",
                };
                acc.push(property);
                return acc;
              }, []);
            },
          },
          {
            id: 4,
            type: "Select",
            defaultOption: "Выберите производителя",
            options: computed(() => brands.value),
            value: null,
            name: "name",
            column: "brand_id",
          },
          {
            id: 5,
            type: "number",
            placeholder: "Цена",
            value: "",
            column: "price",
          },
          {
            id: 6,
            placeholder: "*Описание",
            value: "",
            column: "description",
          },
          {
            id: 7,
            placeholder: "Ссылка на фото",
            value: "",
            column: "photo_link",
          },
        ],
      },
      {
        value: "delete",
        name: "Удалить",
        fields: [
          {
            id: 2,
            type: "number",
            placeholder: "Код товара",
            value: "",
            column: "code",
          },
        ],
      }
    ],
    handler: async (i: number) => {
      const action = listActiveActions.value[i];
      if (action.value === "add") {
        const name = action.fields[0].value;
        const code = action.fields[1].value;
        const category_id = +action.fields[2].value?.id;
        const brand_id = +action.fields[3].value?.id;
        const price = +action.fields[4].value;
        const description = action.fields[5].value || null;
        const count = 100;
        const photo_link = action.fields[6].value;

        const properties = action.additionalFields.reduce((acc, f) => {
          const prop = {
            name: f.name,
            value: f.value,
          };
          acc.push(prop);
          return acc;
        }, []);


        const data: Product = {
          name,
          code,
          category_id,
          brand_id,
          price,
          description,
          count,
          photo_link,
          properties,
          discount: null,
        }

        await createProduct(data);
      } else {
        const code = +action.fields[0].value;
        openModal.value = true;
        modalAction.value = async () => {
          await deleteProduct(code);
          openModal.value = false;
        };
      }
    }
  },
]);
const listActiveActions = ref(
    list.value.reduce((acc, item) => {
      acc.push(reactive(item.actions[0]));
      return acc;
    }, [])
);

async function getProperties(category_id: number): Promise<[{}]> {
  return await getCategoryFilters(category_id);
}
</script>

<style scoped lang="scss">
h1 {
  margin-bottom: 20px;
}

h2 {
  margin-bottom: 10px;
}

.actions {
  width: 40%;
  margin-bottom: 15px;
}

.fields {
  display: flex;
  flex-direction: column;
  align-items: start;

}

.fields > * {
  width: 100%;
}

select {
  outline: none;
  border: 1px solid #ddd;
  border-radius: 2px;
  padding: 10px;
  margin: 7px 0;
  width: 100%;
}

button {
  height: fit-content;
  background: #fff;
  color: var(--color-text);
  border: 1px solid #ddd;
  transition: .17s;

  &:hover {
    background: #f7f7f7;
  }
  &:active {
    background: #fafafa;
  }
}
</style>