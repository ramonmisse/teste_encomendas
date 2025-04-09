import React, { useState } from "react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Card, CardContent } from "@/components/ui/card";
import OrderListing from "./OrderListing";
import OrderForm from "./OrderForm";
import AdminPanel from "./AdminPanel";
import { Bell, Settings, User } from "lucide-react";
import { Button } from "./ui/button";

const Home = () => {
  const [activeTab, setActiveTab] = useState("orders");

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="border-b bg-card">
        <div className="container mx-auto px-4 py-4 flex justify-between items-center">
          <h1 className="text-2xl font-bold text-primary">
            Custom Jewelry Order Management
          </h1>
          <div className="flex items-center space-x-4">
            <Button variant="ghost" size="icon">
              <Bell className="h-5 w-5" />
            </Button>
            <Button variant="ghost" size="icon">
              <Settings className="h-5 w-5" />
            </Button>
            <Button variant="ghost" size="icon">
              <User className="h-5 w-5" />
            </Button>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="container mx-auto px-4 py-6">
        <Tabs
          defaultValue="orders"
          value={activeTab}
          onValueChange={setActiveTab}
          className="w-full"
        >
          <div className="flex justify-between items-center mb-6">
            <TabsList className="grid grid-cols-3 w-[400px]">
              <TabsTrigger value="orders">Order Listing</TabsTrigger>
              <TabsTrigger value="new-order">Create Order</TabsTrigger>
              <TabsTrigger value="admin">Admin Panel</TabsTrigger>
            </TabsList>

            {activeTab === "orders" && (
              <Button
                onClick={() => setActiveTab("new-order")}
                className="bg-primary text-primary-foreground"
              >
                Create New Order
              </Button>
            )}
          </div>

          <Card>
            <CardContent className="p-6">
              <TabsContent value="orders" className="mt-0">
                <OrderListing />
              </TabsContent>

              <TabsContent value="new-order" className="mt-0">
                <OrderForm />
              </TabsContent>

              <TabsContent value="admin" className="mt-0">
                <AdminPanel />
              </TabsContent>
            </CardContent>
          </Card>
        </Tabs>
      </main>

      {/* Footer */}
      <footer className="border-t bg-card py-4 mt-auto">
        <div className="container mx-auto px-4 text-center text-sm text-muted-foreground">
          &copy; {new Date().getFullYear()} Custom Jewelry Order Management
          System
        </div>
      </footer>
    </div>
  );
};

export default Home;
