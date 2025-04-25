<?php

namespace App\Repositories\Warehouse;

use App\Models\HR\Area;
use App\Models\Warehouse\Deploy;
use App\Models\Warehouse\RequestOrder;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeployRepository extends Repository {

    private $stockRepository;
    private $stockHistoryRepository;
    private $requestOrder;

    public function __construct(Deploy $model, StockRepository $stockRepository, StockHistoryRepository $stockHistoryRepository, RequestOrder $requestOrder) {
        $this->stockHistoryRepository = $stockHistoryRepository;
        $this->stockRepository = $stockRepository;
        $this->requestOrder = $requestOrder;
        parent::__construct($model);

    }

    public function returnTool($id){
            
        try {
            $deploy = $this->model()->find($id);
    
            $deploy->is_returned = 1;
        
            $deploy->save();
    
            DB::table('stocks')->where('id', $deploy->stock_id)->update([
                'quantity' => DB::raw('quantity + ' . $deploy->quantity),
                'used' => DB::raw('used - ' . $deploy->quantity),
            ]);
    
            return $deploy;
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function getTools($params) {


        $tools = $this->model()->with(['stock' => function ($query) {
            $query->with(['item' => function ($query) {
                $query->with(['category']);
                // add area
            }]);
        }, 'area', 'item'])->whereHas('stock', function ($query) {
            $query->whereHas('item', function ($query) {
                $query->whereHas('category', function ($query) {
                    $query->where('name', 'tools');
                });
            });
        })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

        

        if($params->search) {

            $tools = $this->model()->with(['stock' => function ($query) {
                $query->with(['item' => function ($query) {
                    $query->with(['category']);
                    // add area
                }]);
            }])->whereHas('stock', function ($query) {
                $query->whereHas('item', function ($query) {
                    $query->whereHas('category', function ($query) {
                        $query->where('name', 'tools');
                    });
                });
            })->where('name', 'like', '%' . $params->search . '%')->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);

            return $tools;
        }
        return $tools;

    }

    public function getReturnTools() {

        $deployedTools = $this->model()->with(['stock' => function ($query) {
            $query->with(['item' => function ($query) {
                $query->with(['category']);
            }]);
        }, 'area'])->whereHas('stock', function ($query) {
            $query->whereHas('item', function ($query) {
                $query->whereHas('category', function ($query) {
                    $query->where('name', 'tools');
                });
            });
        })->where('is_returned', 0)->get();

        


        return $deployedTools;

    }

    public function getDeploy($params) {
        // Check if the authenticated user has the required role (role_id 1, 2, or 3)
        if (auth()->check() && in_array(auth()->user()->role_id, [1, 2, 3])) {
            $deploy = $this->model()->with(['area', 'stock', 'item'])
                ->whereDate('date', $params->date); // Filter by full date
        
            // Additional condition for role_id 2 (Leadman)
            if (auth()->user()->role_id == 2) {
                $deploy = $deploy->where('leadman_id', Auth::id());
            }
        
            if ($params->search) {
                $deploy = $deploy->where(function ($query) use ($params) {
                    $query->orWhereHas('stock', function ($query) use ($params) {
                        $query->whereHas('item', function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                    $query->orWhereHas('area', function ($query) use ($params) {
                        $query->where(function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                    $query->orWhereHas('item', function ($query) use ($params) {
                        $query->where(function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);
        
                return $deploy;
            }
        
            $deploy = $deploy->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);
        
            return $deploy;
        } else {
            // Unauthorized user, handle accordingly (e.g., return an error response)
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }
    public function getDeployNotReturned($params) {
        // Check if the authenticated user has the required role (role_id 1, 2, or 3)
        if (auth()->check() && in_array(auth()->user()->role_id, [1, 2, 3])) {
            $deploy = $this->model()->with(['area', 'stock', 'item']);
    
            // Additional condition for role_id 2 (Leadman)
            if (auth()->user()->role_id == 2) {
                $deploy = $deploy->where('leadman_id', Auth::id());
            }
    
            if ($params->search) {
                $deploy = $deploy->where(function ($query) use ($params) {
                    $query->orWhereHas('stock', function ($query) use ($params) {
                        $query->whereHas('item', function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                    $query->orWhereHas('area', function ($query) use ($params) {
                        $query->where(function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                    $query->orWhereHas('item', function ($query) use ($params) {
                        $query->where(function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);
    
                return $deploy;
            }
    
            $deploy = $deploy->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);
    
            return $deploy;
        } else {
            // Unauthorized user, handle accordingly (e.g., return an error response)
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    public function getDeployNotReturnedConsumed($params) {
        // Check if the authenticated user has the required role (role_id 1, 2, or 3)
        if (auth()->check() && in_array(auth()->user()->role_id, [1, 2, 3])) {
            $deploy = $this->model()->with(['area', 'stock', 'item']);
    
            // Additional condition for role_id 2 (Leadman)
            if (auth()->user()->role_id == 2) {
                $deploy = $deploy->where('leadman_id', Auth::id());
            }
    
            if ($params->search) {
                $deploy = $deploy->where(function ($query) use ($params) {
                    $query->orWhereHas('stock', function ($query) use ($params) {
                        $query->whereHas('item', function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                    $query->orWhereHas('area', function ($query) use ($params) {
                        $query->where(function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                    $query->orWhereHas('item', function ($query) use ($params) {
                        $query->where(function ($query) use ($params) {
                            $query->where('name', 'LIKE', "%$params->search%");
                        });
                    });
                })->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);
    
                return $deploy;
            }
    
            $deploy = $deploy->orderBy('id', 'desc')->paginate($params->count, ['*'], 'page', $params->page);
    
            return $deploy;
        } else {
            // Unauthorized user, handle accordingly (e.g., return an error response)
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }
    
    
    public function storeDeploy($request) {
        $data = new $this->model();
        $data->stock_id = $request->stock_id;
        $data->item_id = $request->item_id;
        $data->area_id = $request->area_id;
        $data->quantity = $request->quantity;
        $data->date = $request->date;
        // $data->leadman_id = $request->leadman_id;
        // $data->leadman_id = $request->leadman_id;
        $data->leadman_id = Auth::id();

    
        // get remaining stock quantity
        $stock = DB::table('stocks')->where('id', $request->stock_id)->first();
    
        $data->remaining_quantity = $stock->quantity - $request->quantity;
    
        if ($data->save()) {
            // Use the new function to create and save RequestOrder
            $requestOrder = $this->createAndSaveRequestOrder($data);
    
            // Your existing code for updating stock and creating history remains unchanged
            $deploy = [
                'id' => $data->stock_id,
                'quantity' => $data->quantity,
            ];
    
            // $this->stockRepository->deducQuantityAddUsed(json_decode(json_encode($deploy)));
    
            $area = Area::find($data->area_id);
    
            $params = [
                'deploy_id' => $data->id,
                'stock_id' => $data->stock_id,
                'item_id' => $data->item_id,
                'area' => $data->area->name,
                'date' => $data->date,
                'quantity' => $data->quantity,
                'leadman_id' => $data->leadman_id,
            ];
    
            $this->stockHistoryRepository->storeHistory(json_decode(json_encode($params)));
    
            return $requestOrder;
        }
    }
public function createAndSaveRequestOrder($data) {
    $requestOrder = new RequestOrder([
        'stock_id' => $data->stock_id,
        'item_id' => $data->item_id,
        'area_id' => $data->area_id,
        'quantity' => $data->quantity,
        'leadman_id' => $data->leadman_id,
        'remaining_quantity' => $data->remaining_quantity,
        'date' => $data->date,
    ]);

    $requestOrder->save();

    return $requestOrder;
}
    // public function storeDeploy($request) {

    //     $data = new $this->model();
    //     $data->stock_id = $request->stock_id;
    //     $data->item_id = $request->item_id;
    //     $data->area_id = $request->area_id;
    //     $data->quantity = $request->quantity;
    //     $data->date = $request->date;
    //     $data->leadman_id = $request->leadman_id;
    
    //     // get remaining stock quantity
    //     $stock = DB::table('stocks')->where('id', $request->stock_id)->first();
    
    //     $data->remaining_quantity = $stock->quantity - $request->quantity;
    
    //     if ($data->save()) {
    
    //         // Create a new RequestOrder instance
    //         $requestOrder = new RequestOrder([
    //             'stock_id' => $data->stock_id,
    //             'item_id' => $data->item_id,
    //             'area_id' => $data->area_id,
    //             'quantity' => $data->quantity,
    //             'leadman_id' => $data->leadman_id,
    //             'remaining_quantity' => $data->remaining_quantity,
    //             'date' => $data->date,
    //         ]);
    
    //         // Save the RequestOrder
    //         $requestOrder->save();
    
    //         // Your existing code for updating stock and creating history remains unchanged
    
    //         $deploy = [
    //             'id' => $data->stock_id,
    //             'quantity' => $data->quantity,
    //         ];
    
    //         $this->stockRepository->deducQuantityAddUsed(json_decode(json_encode($deploy)));
    
    //         $area = Area::find($data->area_id);
    
    //         $params = [
    //             'deploy_id' => $data->id,
    //             'stock_id' => $data->stock_id,
    //             'item_id' => $data->item_id,
    //             'area' => $data->area->name,
    //             'date' => $data->date,
    //             'quantity' => $data->quantity,
    //             'leadman_id' => $data->leadman_id,
    //         ];
    
    //         $this->stockHistoryRepository->storeHistory(json_decode(json_encode($params)));
    //         return $requestOrder;
    //     }
    // }
    
    
    public function updateDeploy($id, $request) {

        $data = $this->model()->find($id);

        $stock_id = $request->stock_id_id ? $request->stock_id_id : $request->stock_id;
        $area_id = $request->area_id_id ? $request->area_id_id : $request->area_id;


        $data->stock_id = $stock_id;
        $data->area_id = $area_id;
        $data->item_id = $request->item_id;
        $data->quantity = $request->quantity;
        $data->date = $request->date;
        $data->leadman_id = $request->leadman_id;

        if($data->save()) {

            $params = [
                'deploy_id' => $data->id,
                'stock_id' => $data->stock_id,
                'item_id' => $data->item_id,
                'area' => $data->area->name,
                'date' => $data->date,
                'quantity' => $data->quantity,
                'leadman_id' => $data->leadman_id,
            ];

            $area = Area::find($data->area_id);

            $history = $this->stockHistoryRepository->updateHistory(json_decode(json_encode($params)));

            return $this->model()->with(['area', 'stock', 'item'])->find($id);
        }

    }

    public function deleteDeploy($id) {

        $data = $this->model()->find($id);

        if($data->delete()) {
            return 'delete';
        }

    }
}