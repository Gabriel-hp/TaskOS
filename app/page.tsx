"use client"

import { useAuth } from '@/contexts/auth-context'
import LoginForm from '@/components/login-form'
import MainLayout from '@/components/main-layout'

export default function Home() {
  const { user } = useAuth()

  if (!user) {
    return <LoginForm />
  }

  return <MainLayout />
}
