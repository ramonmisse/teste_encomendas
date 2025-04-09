import React, { useState } from "react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Textarea } from "@/components/ui/textarea";
import { PlusCircle, Pencil, Trash2, Upload } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";

interface ModelType {
  id: string;
  name: string;
  imageUrl: string;
  description?: string;
}

interface SalesRepType {
  id: string;
  name: string;
  email: string;
  phone?: string;
  avatarUrl?: string;
}

const AdminPanel = () => {
  // Mock data for models
  const [models, setModels] = useState<ModelType[]>([
    {
      id: "1",
      name: "Classic Ring",
      imageUrl:
        "https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=300&q=80",
      description: "Traditional ring design with customizable gemstone",
    },
    {
      id: "2",
      name: "Modern Bracelet",
      imageUrl:
        "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=300&q=80",
      description: "Contemporary bracelet with adjustable links",
    },
    {
      id: "3",
      name: "Pendant Necklace",
      imageUrl:
        "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=300&q=80",
      description: "Elegant pendant with customizable chain length",
    },
  ]);

  // Mock data for sales representatives
  const [salesReps, setSalesReps] = useState<SalesRepType[]>([
    {
      id: "1",
      name: "Maria Silva",
      email: "maria.silva@example.com",
      phone: "(11) 98765-4321",
      avatarUrl: "https://api.dicebear.com/7.x/avataaars/svg?seed=maria",
    },
    {
      id: "2",
      name: "João Santos",
      email: "joao.santos@example.com",
      phone: "(11) 91234-5678",
      avatarUrl: "https://api.dicebear.com/7.x/avataaars/svg?seed=joao",
    },
    {
      id: "3",
      name: "Ana Oliveira",
      email: "ana.oliveira@example.com",
      phone: "(11) 99876-5432",
      avatarUrl: "https://api.dicebear.com/7.x/avataaars/svg?seed=ana",
    },
  ]);

  // State for dialogs
  const [modelDialogOpen, setModelDialogOpen] = useState(false);
  const [repDialogOpen, setRepDialogOpen] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [currentModel, setCurrentModel] = useState<ModelType | null>(null);
  const [currentRep, setCurrentRep] = useState<SalesRepType | null>(null);

  // Model form state
  const [modelForm, setModelForm] = useState({
    name: "",
    imageUrl: "",
    description: "",
  });

  // Rep form state
  const [repForm, setRepForm] = useState({
    name: "",
    email: "",
    phone: "",
    avatarUrl: "",
  });

  // Handle model form changes
  const handleModelFormChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
  ) => {
    const { name, value } = e.target;
    setModelForm((prev) => ({ ...prev, [name]: value }));
  };

  // Handle rep form changes
  const handleRepFormChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setRepForm((prev) => ({ ...prev, [name]: value }));
  };

  // Open model dialog for adding
  const openAddModelDialog = () => {
    setIsEditing(false);
    setCurrentModel(null);
    setModelForm({ name: "", imageUrl: "", description: "" });
    setModelDialogOpen(true);
  };

  // Open model dialog for editing
  const openEditModelDialog = (model: ModelType) => {
    setIsEditing(true);
    setCurrentModel(model);
    setModelForm({
      name: model.name,
      imageUrl: model.imageUrl,
      description: model.description || "",
    });
    setModelDialogOpen(true);
  };

  // Open rep dialog for adding
  const openAddRepDialog = () => {
    setIsEditing(false);
    setCurrentRep(null);
    setRepForm({ name: "", email: "", phone: "", avatarUrl: "" });
    setRepDialogOpen(true);
  };

  // Open rep dialog for editing
  const openEditRepDialog = (rep: SalesRepType) => {
    setIsEditing(true);
    setCurrentRep(rep);
    setRepForm({
      name: rep.name,
      email: rep.email,
      phone: rep.phone || "",
      avatarUrl: rep.avatarUrl || "",
    });
    setRepDialogOpen(true);
  };

  // Save model
  const saveModel = () => {
    if (isEditing && currentModel) {
      // Update existing model
      setModels(
        models.map((model) =>
          model.id === currentModel.id ? { ...model, ...modelForm } : model,
        ),
      );
    } else {
      // Add new model
      const newModel: ModelType = {
        id: Date.now().toString(),
        ...modelForm,
      };
      setModels([...models, newModel]);
    }
    setModelDialogOpen(false);
  };

  // Save rep
  const saveRep = () => {
    if (isEditing && currentRep) {
      // Update existing rep
      setSalesReps(
        salesReps.map((rep) =>
          rep.id === currentRep.id ? { ...rep, ...repForm } : rep,
        ),
      );
    } else {
      // Add new rep
      const newRep: SalesRepType = {
        id: Date.now().toString(),
        ...repForm,
      };
      setSalesReps([...salesReps, newRep]);
    }
    setRepDialogOpen(false);
  };

  // Delete model
  const deleteModel = (id: string) => {
    setModels(models.filter((model) => model.id !== id));
  };

  // Delete rep
  const deleteRep = (id: string) => {
    setSalesReps(salesReps.filter((rep) => rep.id !== id));
  };

  return (
    <div className="bg-background p-6 rounded-lg shadow-sm w-full">
      <h1 className="text-2xl font-bold mb-6">Admin Panel</h1>

      <Tabs defaultValue="models" className="w-full">
        <TabsList className="mb-4">
          <TabsTrigger value="models">Product Models</TabsTrigger>
          <TabsTrigger value="reps">Sales Representatives</TabsTrigger>
        </TabsList>

        {/* Models Tab */}
        <TabsContent value="models">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Product Models</CardTitle>
                <CardDescription>
                  Manage jewelry product models available for orders.
                </CardDescription>
              </div>
              <Button onClick={openAddModelDialog}>
                <PlusCircle className="mr-2 h-4 w-4" />
                Add Model
              </Button>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Preview</TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Description</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {models.map((model) => (
                    <TableRow key={model.id}>
                      <TableCell>
                        <div className="h-16 w-16 rounded-md overflow-hidden">
                          <img
                            src={model.imageUrl}
                            alt={model.name}
                            className="h-full w-full object-cover"
                          />
                        </div>
                      </TableCell>
                      <TableCell className="font-medium">
                        {model.name}
                      </TableCell>
                      <TableCell>{model.description}</TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-2">
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => openEditModelDialog(model)}
                          >
                            <Pencil className="h-4 w-4" />
                          </Button>
                          <Button
                            variant="destructive"
                            size="sm"
                            onClick={() => deleteModel(model.id)}
                          >
                            <Trash2 className="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>

        {/* Sales Representatives Tab */}
        <TabsContent value="reps">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Sales Representatives</CardTitle>
                <CardDescription>
                  Manage sales representatives who handle customer orders.
                </CardDescription>
              </div>
              <Button onClick={openAddRepDialog}>
                <PlusCircle className="mr-2 h-4 w-4" />
                Add Representative
              </Button>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Avatar</TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Phone</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {salesReps.map((rep) => (
                    <TableRow key={rep.id}>
                      <TableCell>
                        <Avatar>
                          <AvatarImage src={rep.avatarUrl} alt={rep.name} />
                          <AvatarFallback>
                            {rep.name.substring(0, 2).toUpperCase()}
                          </AvatarFallback>
                        </Avatar>
                      </TableCell>
                      <TableCell className="font-medium">{rep.name}</TableCell>
                      <TableCell>{rep.email}</TableCell>
                      <TableCell>{rep.phone}</TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-2">
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => openEditRepDialog(rep)}
                          >
                            <Pencil className="h-4 w-4" />
                          </Button>
                          <Button
                            variant="destructive"
                            size="sm"
                            onClick={() => deleteRep(rep.id)}
                          >
                            <Trash2 className="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      {/* Model Dialog */}
      <Dialog open={modelDialogOpen} onOpenChange={setModelDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>
              {isEditing ? "Edit Product Model" : "Add New Product Model"}
            </DialogTitle>
            <DialogDescription>
              {isEditing
                ? "Update the details of this product model."
                : "Add a new product model to the system."}
            </DialogDescription>
          </DialogHeader>
          <div className="grid gap-4 py-4">
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="name" className="text-right">
                Name
              </Label>
              <Input
                id="name"
                name="name"
                value={modelForm.name}
                onChange={handleModelFormChange}
                className="col-span-3"
              />
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="imageUrl" className="text-right">
                Image URL
              </Label>
              <div className="col-span-3 flex gap-2">
                <Input
                  id="imageUrl"
                  name="imageUrl"
                  value={modelForm.imageUrl}
                  onChange={handleModelFormChange}
                  className="flex-1"
                />
                <Button variant="outline" size="icon">
                  <Upload className="h-4 w-4" />
                </Button>
              </div>
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="description" className="text-right">
                Description
              </Label>
              <Textarea
                id="description"
                name="description"
                value={modelForm.description}
                onChange={handleModelFormChange}
                className="col-span-3"
              />
            </div>
            {modelForm.imageUrl && (
              <div className="grid grid-cols-4 items-center gap-4">
                <div className="text-right">Preview</div>
                <div className="col-span-3 h-32 w-32 rounded-md overflow-hidden">
                  <img
                    src={modelForm.imageUrl}
                    alt="Preview"
                    className="h-full w-full object-cover"
                  />
                </div>
              </div>
            )}
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setModelDialogOpen(false)}>
              Cancel
            </Button>
            <Button onClick={saveModel}>
              {isEditing ? "Update" : "Add"} Model
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Rep Dialog */}
      <Dialog open={repDialogOpen} onOpenChange={setRepDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>
              {isEditing
                ? "Edit Sales Representative"
                : "Add New Sales Representative"}
            </DialogTitle>
            <DialogDescription>
              {isEditing
                ? "Update the details of this sales representative."
                : "Add a new sales representative to the system."}
            </DialogDescription>
          </DialogHeader>
          <div className="grid gap-4 py-4">
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="rep-name" className="text-right">
                Name
              </Label>
              <Input
                id="rep-name"
                name="name"
                value={repForm.name}
                onChange={handleRepFormChange}
                className="col-span-3"
              />
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="email" className="text-right">
                Email
              </Label>
              <Input
                id="email"
                name="email"
                type="email"
                value={repForm.email}
                onChange={handleRepFormChange}
                className="col-span-3"
              />
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="phone" className="text-right">
                Phone
              </Label>
              <Input
                id="phone"
                name="phone"
                value={repForm.phone}
                onChange={handleRepFormChange}
                className="col-span-3"
              />
            </div>
            <div className="grid grid-cols-4 items-center gap-4">
              <Label htmlFor="avatarUrl" className="text-right">
                Avatar URL
              </Label>
              <div className="col-span-3 flex gap-2">
                <Input
                  id="avatarUrl"
                  name="avatarUrl"
                  value={repForm.avatarUrl}
                  onChange={handleRepFormChange}
                  className="flex-1"
                />
                <Button variant="outline" size="icon">
                  <Upload className="h-4 w-4" />
                </Button>
              </div>
            </div>
            {repForm.avatarUrl && (
              <div className="grid grid-cols-4 items-center gap-4">
                <div className="text-right">Preview</div>
                <div className="col-span-3">
                  <Avatar className="h-16 w-16">
                    <AvatarImage src={repForm.avatarUrl} alt="Preview" />
                    <AvatarFallback>
                      {repForm.name.substring(0, 2).toUpperCase()}
                    </AvatarFallback>
                  </Avatar>
                </div>
              </div>
            )}
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setRepDialogOpen(false)}>
              Cancel
            </Button>
            <Button onClick={saveRep}>
              {isEditing ? "Update" : "Add"} Representative
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
};

export default AdminPanel;
