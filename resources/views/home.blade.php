<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>نظام الأغراض المفقودة - Prototype</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#e5e7eb',
                        success: '#10b981',
                        warning: '#f59e0b',
                        danger: '#ef4444',
                    },
                    fontFamily: {
                        arabic: ['system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
                    },
                    boxShadow: {
                        card: '0 2px 8px rgba(0, 0, 0, 0.05)',
                    }
                }
            }
        }
    </script>

    <!-- Vue 3 من CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

    <!-- Axios من CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        body {
            font-family: 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', sans-serif;
            direction: rtl;
        }

        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 0.3s ease;
        }

        .fade-enter-from,
        .fade-leave-to {
            opacity: 0;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <div id="app" class="container mx-auto p-4 md:p-6">
        <!-- العنوان الرئيسي -->
        <header class="mb-8">
            <div class="bg-white rounded-xl shadow-card p-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    نظام إدارة الأغراض المفقودة (Prototype)
                </h1>
                <p class="text-gray-600 text-sm md:text-base">
                    هذا نموذج أولي بسيط يوضح فكرة النظام: تسجيل الأغراض المفقودة في نقاط الاستلام،
                    وإتاحة البحث للحاج/المعتمر برقم الهوية أو الجوال أو الباركود، مع طلب استلام إلكتروني،
                    وعرض خريطة نقطة الاستلام.
                </p>
            </div>
        </header>

        <!-- تبديل بين واجهة الحاج وواجهة الموظف -->
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-card p-4">
                <div class="flex flex-wrap gap-3">
                    <!-- زر واجهة الحاج -->
                    <button @click="mode = 'search'"
                        :class="mode === 'search'
                            ?
                            'bg-primary text-white' :
                            'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="flex-1 min-w-[200px] py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg v-if="mode === 'search'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        واجهة الحاج / المعتمر (بحث)
                    </button>

                    <!-- زر واجهة الأدمن مع بادج بعدد الطلبات المعلقة -->
                    <button @click="mode = 'admin'"
                        :class="mode === 'admin'
                            ?
                            'bg-primary text-white' :
                            'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="flex-1 min-w-[200px] py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg v-if="mode === 'admin'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        واجهة الموظف / الأدمن

                        <span v-if="pendingClaimsCount"
                            class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full bg-danger text-white">
                            @{{ pendingClaimsCount }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- واجهة الحاج -->
        <transition name="fade" mode="out-in">
            <div v-if="mode === 'search'" class="space-y-6">

                <!-- بطاقة بحث الباركود -->
                <div class="bg-white rounded-xl shadow-card p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L2 18V4z"></path>
                            <path d="M14 2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2V2z"></path>
                        </svg>
                        البحث باستخدام الباركود
                    </h2>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            رمز الباركود على السوار / الحقيبة (إن وجد):
                        </label>
                        <div class="relative">
                            <input v-model="searchForm.barcode" placeholder="امسح الباركود أو أدخل رقمه"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <div class="absolute left-3 top-3">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M1 12.5A4.5 4.5 0 005.5 17H15a4 4 0 001.866-7.539 3.504 3.504 0 00-4.504-4.272A4.5 4.5 0 004.06 8.235 4.502 4.502 0 001 12.5z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd"></path>
                        </svg>
                        البحث عن غرض مفقود
                    </h2>
                    <form @submit.prevent="searchLostItems" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    رقم الهوية (إن وجد):
                                </label>
                                <input v-model="searchForm.owner_id_number" placeholder="أدخل رقم الهوية"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    رقم الجوال (إن وجد):
                                </label>
                                <input v-model="searchForm.owner_phone" placeholder="أدخل رقم الجوال"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full md:w-auto px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            بحث
                        </button>
                    </form>
                </div>

                <!-- نتائج البحث -->
                <div v-if="searched" class="bg-white rounded-xl shadow-card p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        نتائج البحث
                        <span class="text-sm font-normal text-gray-500">(@{{ searchResults.length }} نتيجة)</span>
                    </h3>

                    <!-- آخر كود طلب استلام -->
                    <div v-if="lastClaimCode" class="mb-4 p-3 rounded-lg bg-green-50 text-green-800 text-sm">
                        تم إنشاء طلب استلام. كود الطلب:
                        <span class="font-mono font-bold">@{{ lastClaimCode }}</span>
                        <span class="text-gray-500 block text-xs mt-1">
                            يرجى إبرازه لموظف نقطة الاستلام عند الحضور.
                        </span>
                    </div>

                    <div v-if="searchResults.length === 0" class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-gray-500">لا توجد أغراض مطابقة للبيانات المدخلة.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="item in searchResults" :key="item.id"
                            class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-gray-800">@{{ item.title }}</h4>
                                        <span
                                            :class="{
                                                'bg-green-100 text-green-800': item.status === 'delivered',
                                                'bg-blue-100 text-blue-800': item.status === 'received'
                                            }"
                                            class="px-3 py-1 rounded-full text-xs font-medium">
                                            @{{ item.status === 'received' ? 'موجود في نقطة الاستلام' : 'تم التسليم' }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-3">@{{ item.description }}</p>
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            @{{ item.pickup_point ? item.pickup_point.name : 'غير محدد' }}
                                        </div>
                                        <div v-if="item.owner_phone" class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                                </path>
                                            </svg>
                                            @{{ item.owner_phone }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-stretch gap-2 md:items-end md:min-w-[180px]">
                                    <!-- زر عرض الخريطة -->
                                    <button v-if="item.pickup_point && item.pickup_point.map_url" type="button"
                                        @click="showPickupPointMap(item.pickup_point)"
                                        class="px-3 py-2 text-xs md:text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center gap-1 justify-center">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        عرض موقع نقطة الاستلام على الخريطة
                                    </button>

                                    <!-- زر طلب استلام -->
                                    <button
                                        class="px-4 py-2 bg-primary text-white rounded-lg text-xs md:text-sm hover:bg-blue-700 transition-colors duration-200"
                                        @click="requestClaim(item)">
                                        طلب استلام هذا الغرض
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- واجهة الموظف / الأدمن -->
            <div v-else class="space-y-6">

                <!-- قسم إضافة نقطة استلام -->
                <div class="bg-white rounded-xl shadow-card p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        إضافة نقطة استلام جديدة
                    </h2>

                    <form @submit.prevent="addPickupPoint" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    اسم النقطة <span class="text-red-500">*</span>
                                </label>
                                <input v-model="pickupPointForm.name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    المدينة
                                </label>
                                <input v-model="pickupPointForm.city"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    العنوان
                                </label>
                                <input v-model="pickupPointForm.address"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>

                        <!-- حقل رابط خريطة جوجل -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                رابط خريطة جوجل (Embed URL)
                            </label>
                            <input v-model="pickupPointForm.map_url"
                                placeholder="مثال: https://www.google.com/maps/embed?pb=..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">
                                الرجاء لصق رابط <span class="font-mono">src</span> من كود التضمين في خرائط جوجل
                                (يبدأ بـ <span class="font-mono">https://www.google.com/maps/embed?pb=...</span>).
                            </p>
                        </div>

                        <button type="submit"
                            class="px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            حفظ نقطة الاستلام
                        </button>
                    </form>

                    <!-- قائمة نقاط الاستلام -->
                    <div v-if="pickupPoints.length" class="mt-8">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">النقاط المسجلة</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="p in pickupPoints" :key="p.id"
                                class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 flex flex-col gap-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <h4 class="font-bold text-gray-800 text-sm">@{{ p.name }}</h4>
                                </div>
                                <p class="text-sm text-gray-600 mb-0.5">@{{ p.city }}</p>
                                <p class="text-xs text-gray-500 mb-2">@{{ p.address }}</p>

                                <button v-if="p.map_url" type="button" @click="showPickupPointMap(p)"
                                    class="mt-auto inline-flex items-center justify-center px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                    عرض الخريطة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قسم إضافة غرض مفقود -->
                <div class="bg-white rounded-xl shadow-card p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z">
                            </path>
                            <path
                                d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h4v1a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H20a1 1 0 001-1V5a1 1 0 00-1-1H3z">
                            </path>
                        </svg>
                        إضافة غرض مفقود جديد
                    </h2>

                    <form @submit.prevent="addLostItem" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    رمز الباركود
                                </label>
                                <input v-model="lostItemForm.barcode" placeholder="أدخل أو امسح رقم الباركود"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    نقطة الاستلام <span class="text-red-500">*</span>
                                </label>
                                <select v-model="lostItemForm.pickup_point_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option disabled value="">اختر نقطة</option>
                                    <option v-for="p in pickupPoints" :key="p.id" :value="p.id">
                                        @{{ p.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    نوع الغرض
                                </label>
                                <select v-model="lostItemForm.item_type_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">اختر نوع الغرض (اختياري)</option>
                                    <option v-for="t in itemTypes" :key="t.id" :value="t.id">
                                        @{{ t.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    عنوان الغرض <span class="text-red-500">*</span>
                                </label>
                                <input v-model="lostItemForm.title" required placeholder="مثال: حقيبة سفر سوداء"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    الوصف
                                </label>
                                <textarea v-model="lostItemForm.description" rows="2"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    رقم هوية المالك
                                </label>
                                <input v-model="lostItemForm.owner_id_number"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    رقم جوال المالك
                                </label>
                                <input v-model="lostItemForm.owner_phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                        <button type="submit"
                            class="px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            حفظ الغرض
                        </button>
                    </form>
                </div>

                <!-- قائمة الأغراض المسجلة -->
                <div class="bg-white rounded-xl shadow-card p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z">
                            </path>
                            <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"></path>
                        </svg>
                        قائمة الأغراض المسجلة
                        <span class="text-sm font-normal text-gray-500">(@{{ lostItems.length }} غرض)</span>
                    </h2>

                    <div v-if="lostItems.length === 0" class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-gray-500">لا توجد أغراض مسجلة بعد.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="item in lostItems" :key="item.id"
                            class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-3">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-lg mb-1">@{{ item.title }}</h4>
                                    <p class="text-gray-600 text-sm mb-2">@{{ item.description }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        :class="{
                                            'bg-green-100 text-green-800': item.status === 'delivered',
                                            'bg-blue-100 text-blue-800': item.status === 'received'
                                        }"
                                        class="px-3 py-1 rounded-full text-xs font-medium">
                                        @{{ item.status === 'received' ? 'موجود في الفرع' : 'تم التسليم' }}
                                    </span>
                                    <div class="text-sm text-gray-500">
                                        @{{ item.pickup_point ? item.pickup_point.name : 'غير محدد' }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-4">
                                <div v-if="item.barcode" class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M1 4h2v12H1V4zm4 0h1v12H5V4zm2 0h2v12H7V4zm4 0h1v12h-1V4zm3 0h1v12h-1V4zm3 0h1v12h-1V4zm2 0h1v12h-1V4z">
                                        </path>
                                    </svg>
                                    باركود: @{{ item.barcode }}
                                </div>
                                <div v-if="item.owner_id_number" class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    هوية: @{{ item.owner_id_number }}
                                </div>
                                <div v-if="item.owner_phone" class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                        </path>
                                    </svg>
                                    جوال: @{{ item.owner_phone }}
                                </div>
                                <div v-if="item.item_type" class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3h12v4H4zM4 9h12v8H4z"></path>
                                    </svg>
                                    نوع الغرض: @{{ item.item_type.name }}
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button v-if="item.status === 'received'" @click="markDelivered(item)"
                                    class="px-4 py-2 bg-green-100 text-green-800 hover:bg-green-200 font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    تحديد كـ تم التسليم
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قسم متابعة طلبات الاستلام -->
                <div class="bg-white rounded-xl shadow-card p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M4 4a2 2 0 012-2h8a2 2 0 012 2v2h-2V4H6v12h8v-2h2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V4z">
                                </path>
                                <path
                                    d="M14.293 8.293a1 1 0 011.414 0L18 10.586l2.293-2.293a1 1 0 111.414 1.414L19.414 12l2.293 2.293a1 1 0 01-1.414 1.414L18 13.414l-2.293 2.293a1 1 0 01-1.414-1.414L16.586 12l-2.293-2.293a1 1 0 010-1.414z">
                                </path>
                            </svg>
                            متابعة طلبات استلام الأغراض
                        </span>

                        <button @click="loadClaimRequests"
                            class="px-3 py-1.5 text-xs md:text-sm bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v2.586l1.707-1.707a1 1 0 011.414 1.414L11.414 10l2.293 2.293a1 1 0 01-1.414 1.414L11 11.414V14a1 1 0 11-2 0v-2.586l-1.707 1.707a1 1 0 01-1.414-1.414L8.586 10 6.293 7.707a1 1 0 011.414-1.414L9 8.586V6a1 1 0 011-1zm-7 9a7 7 0 1114 0h-2a5 5 0 10-10 0H3z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            تحديث
                        </button>
                    </h2>

                    <div v-if="!claimRequests.length" class="text-center py-6">
                        <p class="text-gray-500 text-sm">لا توجد طلبات استلام مسجلة حتى الآن.</p>
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="req in claimRequests" :key="req.id"
                            class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-2">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs text-gray-500">كود الطلب:</span>
                                        <span class="font-mono font-bold text-sm">@{{ req.claim_code }}</span>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        الغرض:
                                        <span class="font-medium">
                                            @{{ req.lost_item ? req.lost_item.title : 'غير محدد' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        نقطة الاستلام:
                                        @{{ req.lost_item && req.lost_item.pickup_point ? req.lost_item.pickup_point.name : 'غير محددة' }}
                                    </div>
                                </div>

                                <div class="flex flex-col items-start md:items-end gap-2">
                                    <span
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': req.status === 'pending',
                                            'bg-green-100 text-green-800': req.status === 'approved',
                                            'bg-red-100 text-red-800': req.status === 'rejected',
                                        }"
                                        class="px-3 py-1 rounded-full text-xs font-medium">
                                        @{{ req.status === 'pending' ?
    'قيد المراجعة' :
    req.status === 'approved' ?
    'مقبول - جاهز للتسليم' :
    'مرفوض' }}
                                    </span>

                                    <div class="text-xs text-gray-500">
                                        مقدّم الطلب:
                                        <span v-if="req.claimant_id_number">
                                            هوية: @{{ req.claimant_id_number }}
                                        </span>
                                        <span v-if="req.claimant_phone">
                                            &nbsp;| جوال: @{{ req.claimant_phone }}
                                        </span>
                                        <span v-if="!req.claimant_id_number && !req.claimant_phone">
                                            بيانات غير متوفرة
                                        </span>
                                    </div>

                                    <div class="text-[11px] text-gray-400">
                                        @{{ req.created_at }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- مودال خريطة نقطة الاستلام -->
        <div v-if="selectedPickupPointForMap" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white w-full max-w-3xl rounded-xl shadow-card overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b">
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm md:text-base">
                            موقع نقطة الاستلام: @{{ selectedPickupPointForMap.name }}
                        </h3>
                        <p v-if="selectedPickupPointForMap.address" class="text-xs text-gray-500 mt-1">
                            @{{ selectedPickupPointForMap.address }}
                        </p>
                    </div>
                    <button @click="closeMap" class="text-gray-500 hover:text-gray-700 text-sm md:text-base">
                        إغلاق ✕
                    </button>
                </div>
                <div class="w-full border-t">
                    <iframe v-if="selectedPickupPointForMap.map_url" :src="selectedPickupPointForMap.map_url"
                        class="w-full border-0" style="height: 65vh;" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" allowfullscreen>
                    </iframe>

                    <div v-else class="p-4 text-center text-sm text-gray-500">
                        لا يوجد رابط خريطة مسجل لهذه النقطة.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        const {
            createApp,
            ref,
            onMounted,
            computed
        } = Vue;

        createApp({
            setup() {
                const mode = ref('search');

                const pickupPoints = ref([]);
                const pickupPointForm = ref({
                    name: '',
                    city: '',
                    address: '',
                    map_url: '',
                });

                const itemTypes = ref([]);

                const lostItems = ref([]);
                const lostItemForm = ref({
                    pickup_point_id: '',
                    item_type_id: '',
                    barcode: '',
                    title: '',
                    description: '',
                    owner_id_number: '',
                    owner_phone: '',
                });

                const searchForm = ref({
                    owner_id_number: '',
                    owner_phone: '',
                    barcode: '',
                });

                const searchResults = ref([]);
                const searched = ref(false);

                const lastClaimCode = ref(null);

                // طلبات الاستلام
                const claimRequests = ref([]);

                // عدد الطلبات المعلقة
                const pendingClaimsCount = computed(() =>
                    claimRequests.value.filter(r => r.status === 'pending').length
                );

                // نقطة الاستلام المختارة لعرض الخريطة
                const selectedPickupPointForMap = ref(null);

                axios.defaults.baseURL = '/api';

                const loadPickupPoints = async () => {
                    const {
                        data
                    } = await axios.get('/pickup-points');
                    pickupPoints.value = data;
                };

                const loadItemTypes = async () => {
                    const {
                        data
                    } = await axios.get('/item-types');
                    itemTypes.value = data;
                };

                const loadLostItems = async () => {
                    const {
                        data
                    } = await axios.get('/lost-items');
                    lostItems.value = data;
                };

                const loadClaimRequests = async () => {
                    const {
                        data
                    } = await axios.get('/claim-requests');
                    claimRequests.value = data;
                };

                const addPickupPoint = async () => {
                    await axios.post('/pickup-points', pickupPointForm.value);
                    pickupPointForm.value = {
                        name: '',
                        city: '',
                        address: '',
                        map_url: '',
                    };
                    await loadPickupPoints();
                };

                const addLostItem = async () => {
                    await axios.post('/lost-items', lostItemForm.value);
                    lostItemForm.value = {
                        pickup_point_id: '',
                        item_type_id: '',
                        barcode: '',
                        title: '',
                        description: '',
                        owner_id_number: '',
                        owner_phone: '',
                    };
                    await loadLostItems();
                };

                const markDelivered = async (item) => {
                    await axios.post(`/lost-items/${item.id}/delivered`);
                    await loadLostItems();
                };

                const searchLostItems = async () => {
                    const {
                        data
                    } = await axios.get('/lost-items/search', {
                        params: searchForm.value,
                    });
                    searchResults.value = data;
                    searched.value = true;
                };

                const requestClaim = async (item) => {
                    try {
                        const {
                            data
                        } = await axios.post(`/lost-items/${item.id}/claim`, {
                            claimant_phone: searchForm.value.owner_phone || null,
                            claimant_id_number: searchForm.value.owner_id_number || null,
                        });

                        lastClaimCode.value = data.claim_code;

                        // تحديث شاشة طلبات الاستلام
                        await loadClaimRequests();

                        alert(`تم إنشاء طلب استلام.\nرمز الطلب: ${data.claim_code}`);
                    } catch (error) {
                        console.error(error);
                        alert('حدث خطأ أثناء إنشاء طلب الاستلام، حاول مرة أخرى.');
                    }
                };

                const showPickupPointMap = (pickupPoint) => {
                    if (!pickupPoint || !pickupPoint.map_url) {
                        alert('لا يوجد رابط خريطة لهذه النقطة.');
                        return;
                    }
                    selectedPickupPointForMap.value = pickupPoint;
                };

                const closeMap = () => {
                    selectedPickupPointForMap.value = null;
                };

                onMounted(async () => {
                    await loadPickupPoints();
                    await loadItemTypes();
                    await loadLostItems();
                    await loadClaimRequests();
                });

                return {
                    mode,
                    pickupPoints,
                    pickupPointForm,
                    itemTypes,
                    lostItems,
                    lostItemForm,
                    searchForm,
                    searchResults,
                    searched,
                    lastClaimCode,
                    claimRequests,
                    pendingClaimsCount,
                    selectedPickupPointForMap,
                    addPickupPoint,
                    addLostItem,
                    markDelivered,
                    searchLostItems,
                    requestClaim,
                    loadClaimRequests,
                    showPickupPointMap,
                    closeMap,
                };
            }
        }).mount('#app');
    </script>
</body>

</html>
