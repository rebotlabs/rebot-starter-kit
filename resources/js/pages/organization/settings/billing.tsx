import { PaymentMethodInfo } from "@/components/billing/payment-method-info"
import { RecentInvoices } from "@/components/billing/recent-invoices"
import { SubscriptionInfo } from "@/components/billing/subscription-info"
import { useTranslation } from "@/hooks/use-i18n"
import { OrganizationSettingsLayout } from "@/layouts/app/organization-settings-layout"
import type { SharedData } from "@/types"
import { Head, usePage } from "@inertiajs/react"

interface PaymentMethod {
  id: string
  type: string
  card?: {
    brand: string
    last4: string
    exp_month: number
    exp_year: number
  }
}

interface Subscription {
  name: string
  stripe_status: string
  stripe_price: string
  quantity: number
  trial_ends_at?: string
  ends_at?: string
  created_at: string
  on_trial: boolean
  canceled: boolean
  on_grace_period: boolean
  recurring: boolean
}

interface Invoice {
  id: string
  date: string
  total: string
  hosted_invoice_url: string
  invoice_pdf: string
}

interface BillingPageProps extends SharedData {
  subscription: Subscription | null
  paymentMethods: PaymentMethod[]
  defaultPaymentMethod: PaymentMethod | null
  invoices: Invoice[]
  billingPortalUrl: string
}

export default function OrganizationSettingsBilling() {
  const t = useTranslation()
  const { props } = usePage<BillingPageProps>()
  const { subscription, defaultPaymentMethod, invoices, billingPortalUrl } = props

  return (
    <OrganizationSettingsLayout>
      <Head title={t("ui.billing.title")} />
      <div className="space-y-6">
        <SubscriptionInfo subscription={subscription} />
        <PaymentMethodInfo defaultPaymentMethod={defaultPaymentMethod} billingPortalUrl={billingPortalUrl} />
        <RecentInvoices invoices={invoices} />
      </div>
    </OrganizationSettingsLayout>
  )
}
